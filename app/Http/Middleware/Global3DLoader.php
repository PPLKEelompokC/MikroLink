<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Global3DLoader
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->environment('testing')) {
            return $response;
        }

        if ($response instanceof \Illuminate\Http\Response && str_contains($response->headers->get('Content-Type'), 'text/html')) {
            $content = $response->getContent();

            $loaderHtml = "
            <style>
                #global-loader {
                    position: fixed; inset: 0; background: #ffffff; z-index: 99999;
                    display: flex; justify-content: center; align-items: center;
                    transition: opacity 0.8s cubic-bezier(0.77, 0, 0.175, 1), visibility 0.8s;
                }
                .logo-premium-intro {
                    width: 200px;
                    animation: premiumPop 1.5s cubic-bezier(0.34, 1.56, 0.64, 1) infinite;
                    filter: drop-shadow(0 0 20px rgba(232, 168, 56, 0.2));
                }
                @keyframes premiumPop {
                    0% { transform: scale(0.5) rotateY(0deg); opacity: 0; filter: blur(10px); }
                    50% { transform: scale(1.1) rotateY(180deg); opacity: 1; filter: blur(0px); }
                    100% { transform: scale(1) rotateY(360deg); opacity: 1; }
                }
                .loader-hidden { opacity: 0; visibility: hidden; }
            </style>
            <div id='global-loader'>
                <img src='" . asset('images/Logo Mikrolink.png') . "' class='logo-premium-intro'>
            </div>
            <script>
                window.addEventListener('load', () => {
                    const loader = document.getElementById('global-loader');
                    setTimeout(() => { 
                        if(loader) loader.classList.add('loader-hidden'); 
                    }, 900);
                });
            </script>";

            $content = preg_replace('/<body([^>]*)>/i', '<body$1>' . $loaderHtml, $content);
            $response->setContent($content);
        }

        return $response;
    }
}