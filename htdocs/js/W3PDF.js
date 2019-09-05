(function($) {
    "use strict";

    var _isInit = false;
    var _baseID = null;
    var _pdf = null;
    var _pageNum = 1;
    var _canvas = null;
    var _context = null;
    var _pageRendering = false;
    var _pageNumPending = null;
    var _scale = 1.5;
    
    function onPrevPage() {
        if (_pageNum <= 1) {
            return;
        }
        --_pageNum;
        queueRenderPage(_pageNum);
    }

    function onNextPage() {
        if (_pageNum >= _pdf.numPages) {
            return;
        }
        ++_pageNum;
        queueRenderPage(_pageNum);
    }

    function queueRenderPage(num) {
        if (_pageRendering) {
            _pageNumPending = num;
        } else {
            renderPage(num);
        }
    }

    function adjustPageControls(num) {
        $("#" + _baseID + "Num").text(num);

        if (num <= 1) {
            $("#" + _baseID + "Pre").attr("disabled", true);
        } else {
            $("#" + _baseID + "Pre").attr("disabled", false);
        }

        if (num >= _pdf.numPages) {
            $("#" + _baseID + "Next").attr("disabled", true);
        } else {
            $("#" + _baseID + "Next").attr("disabled", false);
        }
    }
    
    function renderPage(num) {
        _pageRendering = true;
        _pdf.getPage(num).then(function(page) {
            W3LogDebug("Page rendering: " + num);
            
            var scale = _scale;
            var viewport = page.getViewport({scale: scale});

            _canvas.height = viewport.height;
            _canvas.width = viewport.width;

            var renderContext = {
                canvasContext: _context,
                viewport: viewport
            };
            var renderTask = page.render(renderContext);
            renderTask.promise.then(function () {
                W3LogDebug("Page rendered: " + num);
                
                _pageRendering = false;
                adjustPageControls(num);
                
                if (_pageNumPending !== null) {
                    renderPage(_pageNumPending);
                    _pageNumPending = null;
                }
            });
        });
    }
    
    function InitPDFUIImpl()
    {
        var thisElement = $(this);
        var thisHtml = thisElement.get(0).outerHTML;
        _baseID = thisElement.attr("id");

        var canvasPattern = new RegExp("^<canvas.*canvas>$");
        if (!canvasPattern.test(thisHtml)) {
            W3LogError("Init PDF on wrong element");
            return;
        }

        thisHtml =
            "<div align='center' id='" + _baseID + "WrapperPanel'>" +
            " <canvas id='" + _baseID + "Canvas' style='display:none; border:1px dashed'></canvas>" +
            " <textarea id='" + _baseID + "Log' rows='32' cols='80' style='display:none' disabled='disabled'></textarea>" +
            " <table><tbody><tr>" +
            "  <td><button id='" + _baseID + "Pre' onclick='' type='button' disabled='disabled'>&lt;</button></td>" +
            "  <td><label style='padding-left:5px' id='" + _baseID + "Num'>0</label></td>" +
            "  <td><label> / </label></td>" +
            "  <td><label style='padding-right:5px' id='" + _baseID + "TotalNum'>0</label></td>" +
            "  <td><button id='" + _baseID + "Next' onclick='' type='button' disabled='disabled'>&gt;</button></td>" +
            " </tr></tbody></table>" +
            " <textarea id='" + _baseID + "' style='display:none'></textarea>" +
            "</div>";
        thisElement.replaceWith(thisHtml);

        var nextBtn = document.getElementById(_baseID + "Next");
        if (nextBtn != null) {
            nextBtn.addEventListener('click', onNextPage);
        } else {
            W3LogWarning("PDF next button is not found");
        }

        var preBtn = document.getElementById(_baseID + "Pre");
        if (nextBtn != null) {
            preBtn.addEventListener('click', onPrevPage);
        } else {
            W3LogWarning("PDF previous button is not found");
        }

        _isInit = true;
    }

    function Validate(errorLog)
    {
        return errorLog == "";
    }

    function DisplayErrorLog(errorLog)
    {
        var errorLogDecode = W3DecodePHP(errorLog);
        $("#" + _baseID + "Log").val(errorLogDecode);

        W3DisplayUI(_baseID + "Log");
        W3HideUI(_baseID + "Canvas");

        $("#" + _baseID + "TotalNum").text(0);
        $("#" + _baseID + "Num").text(0);
        $("#" + _baseID + "Pre").attr("disabled", true);
        $("#" + _baseID + "Next").attr("disabled", true);
    }

    function DisplayPDFImpl(noteID, errorLog)
    {
        if (!_isInit) {
            W3LogError("Init PDF UI first!");
        }

        if (!Validate(errorLog)) {
            DisplayErrorLog(errorLog);
            return;
        }

        W3DisplayUI(_baseID + "Canvas");

        var url = "tmp/" + noteID + ".pdf";
        var canvasID = _baseID + "Canvas";
        _canvas = document.getElementById(canvasID);
        if (_canvas == null) {
            W3LogError("No PDF canvas found");
            return;
        }

        _context = _canvas.getContext('2d');
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'pdf/build/pdf.worker.js';

        // Asynchronous download of PDF
        var loadingTask = pdfjsLib.getDocument(url);
        loadingTask.promise.then(
            function(pdf) {
                W3LogDebug('PDF loaded');

                _pdf = pdf;

                $("#" + _baseID + "TotalNum").text(_pdf.numPages);
                _pageNum = 1;
                
                renderPage(_pageNum);
            },
            function (reason) {
                W3LogError(reason);
            }
        );
    }

    $.fn.extend({
        W3DisplayPDF: DisplayPDFImpl,
        W3PDF: InitPDFUIImpl
    });
    
} (jQuery));
