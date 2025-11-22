<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Pagination Helper
 * 
 * Provides pagination functionality for database queries
 */
class Paginator
{
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    
    public function __construct($totalItems, $itemsPerPage = 20, $currentPage = 1)
    {
        $this->totalItems = max(0, (int)$totalItems);
        $this->itemsPerPage = max(1, (int)$itemsPerPage);
        $this->currentPage = max(1, (int)$currentPage);
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        
        // Ensure current page doesn't exceed total pages
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
    }
    
    /**
     * Get SQL LIMIT clause
     */
    public function getLimit()
    {
        return $this->itemsPerPage;
    }
    
    /**
     * Get SQL OFFSET clause
     */
    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    /**
     * Get pagination data for views
     */
    public function getPaginationData()
    {
        return [
            'total_items' => $this->totalItems,
            'items_per_page' => $this->itemsPerPage,
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'has_prev' => $this->currentPage > 1,
            'has_next' => $this->currentPage < $this->totalPages,
            'prev_page' => max(1, $this->currentPage - 1),
            'next_page' => min($this->totalPages, $this->currentPage + 1),
            'start_item' => $this->totalItems > 0 ? $this->getOffset() + 1 : 0,
            'end_item' => min($this->getOffset() + $this->itemsPerPage, $this->totalItems),
            'pages' => $this->getPageNumbers()
        ];
    }
    
    /**
     * Get array of page numbers to display
     */
    private function getPageNumbers()
    {
        $pages = [];
        $range = 2; // Show 2 pages on each side of current
        
        // Always show first page
        $pages[] = 1;
        
        // Calculate range
        $start = max(2, $this->currentPage - $range);
        $end = min($this->totalPages - 1, $this->currentPage + $range);
        
        // Add ellipsis after first page if needed
        if ($start > 2) {
            $pages[] = '...';
        }
        
        // Add pages in range
        for ($i = $start; $i <= $end; $i++) {
            $pages[] = $i;
        }
        
        // Add ellipsis before last page if needed
        if ($end < $this->totalPages - 1) {
            $pages[] = '...';
        }
        
        // Always show last page (if more than 1 page)
        if ($this->totalPages > 1) {
            $pages[] = $this->totalPages;
        }
        
        return $pages;
    }
    
    /**
     * Generate pagination HTML
     */
    public function renderPagination($baseUrl, $additionalParams = [])
    {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $data = $this->getPaginationData();
        $html = '<div class="pagination-wrapper">';
        $html .= '<div class="pagination-info">Showing ' . $data['start_item'] . ' to ' . $data['end_item'] . ' of ' . $data['total_items'] . ' entries</div>';
        $html .= '<div class="pagination">';
        
        // Previous button
        if ($data['has_prev']) {
            $url = $this->buildUrl($baseUrl, $data['prev_page'], $additionalParams);
            $html .= '<a href="' . htmlspecialchars($url) . '" class="page-link">« Previous</a>';
        } else {
            $html .= '<span class="page-link disabled">« Previous</span>';
        }
        
        // Page numbers
        foreach ($data['pages'] as $page) {
            if ($page === '...') {
                $html .= '<span class="page-link disabled">...</span>';
            } elseif ($page == $this->currentPage) {
                $html .= '<span class="page-link active">' . $page . '</span>';
            } else {
                $url = $this->buildUrl($baseUrl, $page, $additionalParams);
                $html .= '<a href="' . htmlspecialchars($url) . '" class="page-link">' . $page . '</a>';
            }
        }
        
        // Next button
        if ($data['has_next']) {
            $url = $this->buildUrl($baseUrl, $data['next_page'], $additionalParams);
            $html .= '<a href="' . htmlspecialchars($url) . '" class="page-link">Next »</a>';
        } else {
            $html .= '<span class="page-link disabled">Next »</span>';
        }
        
        $html .= '</div></div>';
        
        return $html;
    }
    
    /**
     * Build URL with page parameter
     */
    private function buildUrl($baseUrl, $page, $additionalParams = [])
    {
        $params = array_merge($additionalParams, ['page' => $page]);
        $queryString = http_build_query($params);
        
        // Check if base URL already has query string
        $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
        
        return $baseUrl . $separator . $queryString;
    }
}
