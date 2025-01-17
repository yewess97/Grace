@for ($i = 1; $i <= $rating; $i++)
    <span class="position-relative fs-4 star-fill" aria-label="Filled Star">★</span>
@endfor
@for ($j = 1; $j <= 5 - $rating; $j++)
    <span class="position-relative fs-4 star-empty" aria-label="Empty Star">☆</span>
@endfor
