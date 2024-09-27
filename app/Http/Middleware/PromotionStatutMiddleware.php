<?php

namespace App\Http\Middleware;

use App\Repository\PromoFirebaseRepository;
use Closure;

class PromotionStatutMiddleware
{
    protected $promotionFirebaseService;

    public function __construct(PromoFirebaseRepository $promotionFirebaseService)
    {
        $this->promotionFirebaseService = $promotionFirebaseService;
    }

    public function handle($request, Closure $next)
    {
        $promotionId = $request->route('id');
        $promotion = $this->promotionFirebaseService->find($promotionId);

        if (!$promotion) {
            return response()->json(['message' => 'Promotion non trouvée'], 404);
        }

        if ($promotion['etat'] === 'Cloturée') {
            // Bloquer les opérations POST, PATCH, DELETE
            if (in_array($request->method(), ['POST', 'PATCH', 'DELETE'])) {
                return response()->json(['message' => 'Les opérations POST, PATCH, DELETE sont interdites pour une promotion clôturée'], 403);
            }
        }

        return $next($request);
    }
}

