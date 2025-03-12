
<div class="modal fade" id="PDFModal" >
 <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="PDFModalSGDLabel">-</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

    <div role="toolbar" id="toolbar" class="d-none">
        <div id="pager">
            <button class="btn btn-sm btn-info" data-pager="prev"> < </button>
            <button class="btn btn-sm btn-info" data-pager="next"> > </button>
        </div>
        <div id="page-mode">
            <label>
                Página
                <input type="number" value="2" min="1" />
            </label>
        </div>
    </div>
    <div id="viewport-container">
        <div role="main" id="viewport"></div>
    </div>
    </div>
</div>


@push('js')
<script
        src="https://unpkg.com/pdfjs-dist@latest/build/pdf.min.mjs"
        type="module"
    ></script>
<script type="module">
import * as pdfjsLib from 'https://unpkg.com/pdfjs-dist@latest/build/pdf.min.mjs';

// Setting worker src for PDF.js.
pdfjsLib.GlobalWorkerOptions.workerSrc =
	'https://unpkg.com/pdfjs-dist@latest/build/pdf.worker.min.mjs';

(function () {
	let currentPageIndex = 0;
	let pageMode = 1;
	let cursorIndex = Math.floor(currentPageIndex / pageMode);
	let pdfInstance = null;
	let totalPagesCount = 0;

	const viewport = document.querySelector('#viewport');

	window.initPDFViewer = function (pdfURL,nombre) {
        document.querySelector('#PDFModalSGDLabel').innerHTML ='Cargando documento';
        //document.querySelector('#PDFModalSGDLabel').innerHTML ='Cargando documento';
		pdfjsLib.getDocument(pdfURL).promise.then((pdf) => {
			pdfInstance = pdf;
			totalPagesCount = pdf.numPages;
            document.querySelector('#PDFModalSGDLabel').innerHTML = nombre;
			//initPager();
			//initPageMode();
			render();
		});
	};



	function render() {
		cursorIndex = Math.floor(currentPageIndex / pageMode);
		const startPageIndex = cursorIndex * pageMode;
		const endPageIndex = totalPagesCount - 1;
			{{-- startPageIndex + pageMode < totalPagesCount
				? startPageIndex + pageMode - 1
				: totalPagesCount - 1; --}}

		const renderPagesPromises = [];
		for (let i = startPageIndex; i <= endPageIndex; i++) {
			renderPagesPromises.push(pdfInstance.getPage(i + 1));
		}

		Promise.all(renderPagesPromises).then((pages) => {
			// Create containers for each page with appropriate width.
			const pagesHTML = pages
				.map(
					() =>
						`<div style="width: ${
							pageMode > 1 ? '100%' : '100%'
						}"><canvas></canvas></div>`,
				)
				.join('');
			viewport.innerHTML = pagesHTML;
			pages.forEach((page, index) => renderPage(page, index));
		});
	}

	function renderPage(page, pageIndex) {
		let pdfViewport = page.getViewport({ scale: 1 });

		const container = viewport.children[pageIndex]; // Correctly map the page to its container.
		if (!container) {
			console.error('Container not found for page ' + pageIndex);
			return;
		}

		const canvas = container.querySelector('canvas');
		const context = canvas.getContext('2d');

		// Scale canvas to fit container width.
		pdfViewport = page.getViewport({
			scale: container.offsetWidth / pdfViewport.width,
		});

		canvas.height = pdfViewport.height;
		canvas.width = pdfViewport.width;

		page.render({
			canvasContext: context,
			viewport: pdfViewport,
		});
	}
})();
</script>
@endpush