<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\UI;

/**
 * Pagination links builder for Listing.
 * 
 * Pager implementation can return any array with link, 
 * but templates must know how to desplay them.
 */
interface Pager
{
    /**
     * Create pagination links for specified Listing.
     * 
     * @return array
     */
    public function create(Listing $listing);
}
