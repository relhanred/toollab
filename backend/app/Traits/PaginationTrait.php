<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginationTrait
{
    /**
     * Paginer une requête avec les paramètres standard
     *
     * @param Builder $query La requête à paginer
     * @param Request $request La requête HTTP contenant les paramètres de pagination
     * @param int $defaultPerPage Nombre d'éléments par page par défaut
     * @return array Tableau contenant les éléments et les informations de pagination
     */
    protected function paginateQuery(Builder $query, Request $request, int $defaultPerPage = 10): array
    {
        // Récupérer les paramètres de pagination depuis la requête
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', $defaultPerPage);

        // Limiter le nombre d'éléments par page pour éviter les abus
        $perPage = min($perPage, 100);

        // Récupérer le nombre total d'éléments
        $total = $query->count();

        // Paginer les résultats
        $items = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Calculer le nombre total de pages
        $totalPages = ceil($total / $perPage);

        // Retourner les résultats avec les informations de pagination
        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Formater une réponse paginée pour l'API
     *
     * @param LengthAwarePaginator $paginator Le paginateur Laravel
     * @return array Tableau contenant les éléments et les informations de pagination
     */
    protected function formatPaginatedResponse(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'total_pages' => $paginator->lastPage()
            ]
        ];
    }
}
