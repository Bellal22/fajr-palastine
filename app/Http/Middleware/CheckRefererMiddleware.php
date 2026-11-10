<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRefererMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $host = strtolower($request->getHost());

        // Allow opening the page normally if the host is fpya.ps or www.fpya.ps
        if ($host === 'fpya.ps' || $host === 'www.fpya.ps') {
            return $next($request);
        }

        // Enforce the check on the production domain: reg.fajeryouth.org (and its subdomains)
        if ($host === 'reg.fajeryouth.org' || str_ends_with($host, '.reg.fajeryouth.org')) {

            // Allow if user has an active registration session (mid-registration navigation/refresh)
            if ($request->session()->has('id_num')) {
                return $next($request);
            }

            $referer = $request->headers->get('referer');
            if (!$referer) {
                return redirect()->away('https://home.fpya.ps/');
            }

            $refererHost = parse_url($referer, PHP_URL_HOST);
            if (!$refererHost) {
                return redirect()->away('https://home.fpya.ps/');
            }
            $refererHost = strtolower($refererHost);

            // Allowed referer domains: fpya.ps, fajeryouth.org (and all their subdomains)
            $allowed = false;
            $allowedDomains = ['fpya.ps', 'fajeryouth.org'];
            foreach ($allowedDomains as $domain) {
                if ($refererHost === $domain || str_ends_with($refererHost, '.' . $domain)) {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                return redirect()->away('https://home.fpya.ps/');
            }
        }

        return $next($request);
    }
}
