<style>
.tr-pagination {
    display: flex !important;
    justify-content: center;
    margin: 20px 0;
}

@media (max-width: 768px) {
    .tr-pagination {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 10px 0;
    }
    
    .tr-pagination .pagination {
        margin: 0;
        white-space: nowrap;
    }
}
</style>
<div class="tr-pagination">
  <nav aria-label="Page navigation example">
    <ul class="pagination">

      <!-- <div class="d-flex"> -->
      {{-- First Page Link --}}
      @if ($paginator->onFirstPage())
      <li class="page-item">
        <a class="page-link" href="javascript:void(0);" aria-label="Last Previous">
          <span aria-hidden="true">
            <img src="{{ asset('frontend/hotel-detail/images/left-double-arrow.png') }}" alt="First"
              loading="lazy">
          </span>
        </a>
      </li>
      @else
      <li class="page-item">
        <a class="page-link" href="{{ $paginator->url(1) }}" aria-label="Last Previous">
          <span aria-hidden="true">
            <img src="{{ asset('frontend/hotel-detail/images/left-double-arrow.png') }}" alt="First"
              loading="lazy">
          </span>
        </a>
      </li>
      @endif

      {{-- Previous Page Link --}}
      @if ($paginator->onFirstPage())
      <li class="page-item">
        <a class="page-link" href="javascript:void(0);" aria-label="Previous">
          <span aria-hidden="true">
            <img src="{{ asset('frontend/hotel-detail/images/arrow-left.png') }}" alt="Previous"
              class="chevron_back" loading="lazy"></span>
        </a>
      </li>

      @else
      <li class="page-item">
        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" class="arrow-link">
          <span aria-hidden="true">
            <img src="{{ asset('frontend/hotel-detail/images/arrow-left.png') }}" alt="Previous"
              class="chevron_back" loading="lazy"></span>
        </a>
      </li>
      @endif

      {{-- First Page Link --}}
      @if ($paginator->currentPage() > 2)
      <li class="page-item"><a class="page-link " href="{{ $paginator->url(1) }}">1</a></li>
      @endif
      {{-- Previous Page Link --}}
      @if ($paginator->currentPage() > 1)
      <li class="page-item"><a class="page-link "
          href="{{ $paginator->url($paginator->currentPage() - 1) }}">{{ $paginator->currentPage() - 1 }}</a></li>

      @endif

      {{-- Current Page --}}
      <li class="page-item"><a class="page-link active" href="javascript:void(0);">{{ $paginator->currentPage() }}</a>
      </li>
      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
      <li class="page-item"> <a class="page-link"
          href="{{ $paginator->nextPageUrl() }}">{{ $paginator->currentPage() + 1 }}</a></li>

      @endif

      {{-- Last Page Link --}}
      @if ($paginator->currentPage() + 1 < $paginator->lastPage())
        <li class="page-item"> <a class="page-link"
            href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
        </li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
            <span aria-hidden="true"> <img src="{{ asset('frontend/hotel-detail/images/arrow-right.png') }}"
                alt="Next" loading="lazy"></span>
          </a>
        </li>

        @else
        <li class="page-item">
        <a class="page-link" href="javascript:void(0);" aria-label="Next">
         <span aria-hidden="true"> <img src="{{ asset('frontend/hotel-detail/images/arrow-right.png') }}"
              alt="Next" loading="lazy"></span>
        </a>
        </li>
        @endif

        {{-- Last Page Link --}}
        @if ($paginator->hasMorePages())
        <li class="page-item">
          <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" aria-label="Last Next">
            <span aria-hidden="true"> <img
                src="{{ asset('frontend/hotel-detail/images/right-double-arrow.png') }}" alt="Last"
                loading="lazy"></span>
          </a>
        </li>


        @else
        <li class="page-item">
          <a class="page-link" href="javascript:void(0);" aria-label="Last Next">
            <span aria-hidden="true">
              <img src="{{ asset('frontend/hotel-detail/images/right-double-arrow.png') }}" alt="Last"
                loading="lazy">
            </span>
          </a>
        </li>
        @endif
        <!-- </div> -->

    </ul>
  </nav>
</div>