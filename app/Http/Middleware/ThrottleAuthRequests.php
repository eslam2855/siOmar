<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleAuthRequests
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts())) {
            return $this->buildResponse($key);
        }
        
        RateLimiter::hit($key, $this->decayMinutes() * 60);
        
        $response = $next($request);
        
        return $this->addHeaders(
            $response, $this->maxAttempts(),
            $this->calculateRemainingAttempts($key)
        );
    }
    
    /**
     * Resolve request signature.
     */
    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(implode('|', [
            $request->ip(),
            $request->userAgent(),
            $request->input('email', ''),
        ]));
    }
    
    /**
     * Create a 'too many attempts' response.
     */
    protected function buildResponse(string $key): Response
    {
        $retryAfter = RateLimiter::availableIn($key);
        
        $response = response()->json([
            'success' => false,
            'message' => 'Too many login attempts. Please try again in ' . ceil($retryAfter / 60) . ' minutes.',
        ], 429);
        
        $response->headers->set('Retry-After', $retryAfter);
        $response->headers->set('X-RateLimit-Reset', $this->availableAt($retryAfter));
        
        return $response;
    }
    
    /**
     * Add the limit header information to the given response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', $remainingAttempts);
        
        return $response;
    }
    
    /**
     * Calculate the number of remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key): int
    {
        return RateLimiter::remaining($key, $this->maxAttempts());
    }
    
    /**
     * Get the maximum number of attempts for the rate limiter.
     */
    protected function maxAttempts(): int
    {
        return 5; // 5 attempts per window
    }
    
    /**
     * Get the number of minutes to decay the rate limiter.
     */
    protected function decayMinutes(): int
    {
        return 15; // 15 minutes window
    }
    
    /**
     * Get the "available at" timestamp for the response.
     */
    protected function availableAt(int $delay): int
    {
        return now()->addSeconds($delay)->getTimestamp();
    }
}
