var SALES = function () {
    var iframe_docs_preview = $('#iframe_prop_preview',document);
    var iframe_box = $('#iframe_box',document);

    var init_sales = function (dataid) {
        //iframe_docs_preview.attr('src',PECO.base_url() + 'cad/getproposalpdf/' + dataid);

        finalize_document(dataid);
    };

    var sales_handler = function (dataid) {
        var preview_src = iframe_docs_preview.attr('src');
        PECO.select2Basic($('#select2_du',document),'cad/select2du','Distribution Utility...',true,false,false);
        $('#btn_reload_preview',document).on('click',function () {
            create_docs_preview(dataid);
        });

        $('#btn_open_preview',document).on('click',function () {
            var doctype = iframe_box.attr('data-id');
            
            // Create a temporary form in the current document
            const form = document.createElement('form');
            form.method = 'post';
            form.action = PECO.base_url() + 'cad/getdocumentpreview';
            form.target = '_blank'; // Open in new tab

            const idField = document.createElement('input');
            idField.type = 'hidden';
            idField.name = 'id';
            idField.value = dataid;

            form.appendChild(idField);

            const doctypeField = document.createElement('input');
            doctypeField.type = 'hidden';
            doctypeField.name = 'doctype';
            doctypeField.value = doctype;

            form.appendChild(doctypeField);

            // Append form to body, submit, then remove
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });

        $('#frm_rate_update',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data : this_.serialize()  + '&' + $.param({id : dataid})
            }).done(function (d) {
                PECO.initAlerts(d.msg,'Proposed Rate Update',d.func);
            }).fail(function () {
            })
        });

        $(document).on('click','#btn_finalize_document',function () {
            finalize_document(dataid,true);
        });

        $(document).on('click','#btn_regenerate_document',function () {
            var iframe_box = $('#iframe_box',document);
            var doctype = iframe_box.attr('data-id');
            $.ajax({
                url : PECO.base_url() + 'cad/deletedocument',
                type: 'post',
                dataType: 'json',
                data: {
                    id : dataid,
                    doctype : doctype,
                }
            }).done(function (d) {
                if (d.qry) {
                    finalize_document(dataid);
                }
                PECO.initAlerts(d.msg,'New Proposal',d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $('#frm_application_plan_details',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Save Customer Plan?",
                text: "Please confirm saving Customer Plan Details.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Send",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url : this_.attr('action'),
                        type: this_.attr('method'),
                        dataType: 'json',
                        data: this_.serialize() + '&appid=' + dataid
                    }).done(function (d) {
                        swal(d.msg, d.title, d.func);
                        if (d.qry) {
                            create_docs_preview(dataid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal.close();
                }
            });
        });

        PECO.select2Basic($('#select2_planduration',document),'cad/select2planduration','Select plan...',false,false,$('#select2_planduration',document).val(),false,false,dataid);

        $(document).on('change','#select2_planduration',function () {
            var this_ = $(this);
            var this_val = this_.val();

            if (this_val !== '') {
                $.ajax({
                    url: PECO.base_url() + 'cad/getselectedplanamount',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        appid: dataid,
                        duration: this_val
                    }
                }).done(function (d) {
                    if (d.value && d.value > 0) {
                        $('#planamount', document).val(d.value);
                    }
                }).fail(function () {

                });
            } else {
                $('#planamount', document).val('');
            }
        });

        $('input[type=number]').on('keypress',function (evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            var this_ = $(this);
            var value = this_.val();
            if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            } else {
                var count = (value.match(/\./g) || []).length;
                if (charCode == 46 && count > 0) {
                    return false;
                } else {
                    return true;
                }
            }
        });

        init_assigned_so(dataid);
        so_assignment_handler(dataid);
    };

    var init_assigned_so = function (dataid,edit) {
        var application_sales_officer =  $('#application_sales_officer',document);
        var so_tools =  $('#so_tools',document);
        $.ajax({
            //LOOKUP ASSIGNED SALES OFFICER
            url : PECO.base_url() + 'cad/getassigendso',
            type : 'post',
            dataType : 'json',
            data : {
                appid : dataid,
                edit : edit
            }, 
            beforeSend: function () {
                application_sales_officer.html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading...</div>');
            },
        }).done(function (d) {
            //if exists, change div content to
            //console.log(d);

            if (typeof d.soname !== 'undefined') {
                application_sales_officer.html(d.soname);
            }

            if (typeof d.so_input !== 'undefined') {
                application_sales_officer.html(d.so_input);
                PECO.select2Basic($('#select2_sales_officer',document),'cad/select2salesofficer','Select Sales Officer...',false,false,$('#select2_sales_officer',document).val());
                $('#btn_assign_sales',document).removeClass('hidden');
            }

            if (typeof d.buttons !== 'undefined') {
                so_tools.html(d.buttons);
            }

        }).fail(function () {
            PECO.initAlerts('Error Sales Officer Lookup','Error!!!','error');
        });
    }

    var so_assignment_handler = function (dataid) {
        var application_sales_officer =  $('#application_sales_officer',document);
        var so_tools =  $('#so_tools',document);

        $(document).on('click','#btn_assign_sales',function () {
            var sales_officer = $('#select2_sales_officer',document).val();

            if (sales_officer > 0) {
                swal({
                    title: "Assign Sales Officer",
                    text: "Assign selected Sales Officer to this customer?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-primary",
                    confirmButtonText: "Yes!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: PECO.base_url() + 'cad/assignsalesofficer',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                appid: dataid,
                                salesperson: sales_officer
                            }
                        }).done(function (d) {
                            swal(d.msg, 'Assign SO', d.func);
                            if (d.qry) {
                                init_assigned_so(dataid);
                            }
                        }).fail(function () {
                            PECO.phpError();
                        });
                    } else {
                        swal.close();
                    }
                });
            } else {
                swal('Assign SO', 'Please select a Sales Officer to be assigned to this application.', 'warning');
            }
        });

        $(document).on('click','#btn_edit_sales',function () {
            init_assigned_so(dataid,true)
        });

        $(document).on('click','#btn_cancel_edit',function () {
            init_assigned_so(dataid)
        });

        $(document).on('click','#btn_delete_sales',function () {
            swal({
                title: "Remove Sales Officer",
                text: "Remove Sales Officer from this customer?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Delete!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'cad/deletesalesofficer',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            appid: dataid,
                        }
                    }).done(function (d) {
                        swal(d.msg, 'Delete SO', d.func);
                        if (d.qry) {
                            init_assigned_so(dataid);
                        }
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal.close();
                }
            });
        });

    }

    var finalize_proposal = function (dataid,finalize) {
        $.ajax({
            url : PECO.base_url() + 'cad/finalizeproposal',
            type: 'post',
            dataType: 'json',
            data: {
                id : dataid,
                finalize : finalize
            }
        }).done(function (d) {
            if (d.msg.length > 0) {
                PECO.initAlerts(d.msg,'Finalize Document',d.func);
            }
            $('#preview_actions',document).html(d.buttons);
        }).fail(function () {
            PECO.phpError();
        })
    };

    var finalize_document = function (dataid,finalize) {
        var iframe_box = $('#iframe_box',document);
        var doctype = iframe_box.attr('data-id');
        $.ajax({
            url : PECO.base_url() + 'cad/finalizedocument',
            type: 'post',
            dataType: 'json',
            data: {
                id : dataid,
                finalize : finalize,
                doctype : doctype
            }
        }).done(function (d) {
            if (d.msg.length > 0) {
                PECO.initAlerts(d.msg,'Finalize Document',d.func);
            }
            $('#preview_actions',document).html(d.buttons);
        }).fail(function () {
            PECO.phpError();
        })
    };

    var create_docs_preview = function (dataid,params) {
        var iframe_box = $('#iframe_box',document);

        // Remove any existing content (loading indicator, iframe, PDF canvas container, and all canvas elements)
        iframe_box.find('.iframe-loading-indicator, #iframe_doc_preview, #pdf-canvas-container, canvas').remove();
        iframe_box.empty(); // Clear everything to ensure no duplicates
        
        // Show loading indicator
        var loadingIndicator = $('<div class="iframe-loading-indicator" style="text-align: center; padding: 50px; background-color: #f5f5f5; border: 1px solid #ddd; border-radius: 4px; margin: 10px 0;">' +
            '<h3><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Loading document preview...</h3>' +
            '<p class="text-muted">Please wait while the document is being generated.</p>' +
            '</div>');
        iframe_box.append(loadingIndicator);

        // Prepare form data
        var formData = new FormData();
        formData.append('id', dataid);
        formData.append('doctype', iframe_box.attr('data-id'));

        if (params && typeof params === 'object') {
            $.each(params, function(index, value) {
                formData.append(index, value);
            });
        }

        // Function to load PDF.js and render PDF
        var loadAndRenderPDF = function(pdfBlob) {
            // Load PDF.js from CDN if not already loaded
            if (typeof pdfjsLib === 'undefined') {
                var script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                script.onload = function() {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    renderPDF(pdfBlob);
                };
                script.onerror = function() {
                    fallbackToIframe();
                };
                document.head.appendChild(script);
            } else {
                renderPDF(pdfBlob);
            }
        };

        // Function to render PDF using PDF.js
        var renderPDF = function(pdfBlob) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var typedArray = new Uint8Array(e.target.result);
                
                pdfjsLib.getDocument({data: typedArray}).promise.then(function(pdf) {
                    // Create container for PDF viewer
                    var container = $('<div id="pdf-canvas-container" style="width: 100%; height: 75vh; overflow-y: auto; overflow-x: hidden; background: #fff; padding: 0; box-sizing: border-box;"></div>');
                    var canvasContainer = $('<div style="text-align: center; width: 100%;"></div>');
                    
                    // Add container to DOM first to get accurate width
                    iframe_box.append(container);
                    container.append(canvasContainer);
                    
                    // Calculate initial scale based on container width
                    var containerWidth = container.innerWidth();
                    var baseScale = 1.0;
                    var currentScale = baseScale;
                    
                    // Render first page
                    var pageNum = 1;
                    var renderPage = function(num) {
                        pdf.getPage(num).then(function(page) {
                            // Calculate scale to fit container width
                            var viewport = page.getViewport({scale: 1.0});
                            var pageWidth = viewport.width;
                            var calculatedScale = containerWidth / pageWidth;
                            var scale = Math.min(calculatedScale * currentScale, 3.0); // Max scale of 3x
                            
                            var scaledViewport = page.getViewport({scale: scale});
                            var canvas = $('<canvas></canvas>').attr({
                                id: 'pdf-canvas-' + num
                            })[0];
                            var context = canvas.getContext('2d');
                            canvas.height = scaledViewport.height;
                            canvas.width = scaledViewport.width;
                            
                            // Ensure canvas doesn't exceed container width
                            if (canvas.width > containerWidth) {
                                var scaleRatio = containerWidth / canvas.width;
                                canvas.style.width = containerWidth + 'px';
                                canvas.style.height = (canvas.height * scaleRatio) + 'px';
                            } else {
                                canvas.style.width = canvas.width + 'px';
                                canvas.style.height = canvas.height + 'px';
                            }
                            
                            var pageDiv = $('<div style="margin-bottom: 20px; width: 100%; text-align: center;"></div>');
                            pageDiv.append(canvas);
                            canvasContainer.append(pageDiv);
                            
                            var renderContext = {
                                canvasContext: context,
                                viewport: scaledViewport
                            };
                            
                            page.render(renderContext).promise.then(function() {
                                // Render next page if exists
                                if (num < pdf.numPages) {
                                    renderPage(num + 1);
                                } else {
                                    // All pages rendered, show the container
                                    // canvasContainer is already appended
                                    
                                    // Add navigation controls
                                    if (pdf.numPages > 1) {
                                        var controls = $('<div style="text-align: center; padding: 10px; background: #f5f5f5; border-bottom: 1px solid #ddd;">' +
                                            '<button class="btn btn-sm btn-default" id="pdf-prev" style="margin-right: 10px;"><i class="fa fa-arrow-up"></i> Previous</button>' +
                                            '<span id="pdf-page-info" style="margin: 0 10px;">Page 1 of ' + pdf.numPages + '</span>' +
                                            '<button class="btn btn-sm btn-default" id="pdf-next"><i class="fa fa-arrow-down"></i> Next</button>' +
                                            '<button class="btn btn-sm btn-default" id="pdf-zoom-out" style="margin-left: 10px;"><i class="fa fa-search-minus"></i></button>' +
                                            '<button class="btn btn-sm btn-default" id="pdf-zoom-in"><i class="fa fa-search-plus"></i></button>' +
                                            '</div>');
                                        container.prepend(controls);
                                        
                                        var currentPage = 1;
                                        var scrollToPage = function(pageNum) {
                                            var targetCanvas = $('#pdf-canvas-' + pageNum);
                                            if (targetCanvas.length) {
                                                var targetOffset = targetCanvas.offset().top - container.offset().top + container.scrollTop() - 50;
                                                container.animate({
                                                    scrollTop: targetOffset
                                                }, 300);
                                                $('#pdf-page-info').text('Page ' + pageNum + ' of ' + pdf.numPages);
                                                currentPage = pageNum;
                                            }
                                        };
                                        
                                        // Track scroll position to update current page
                                        container.on('scroll', function() {
                                            var scrollTop = container.scrollTop();
                                            var foundPage = 1;
                                            for (var i = 1; i <= pdf.numPages; i++) {
                                                var canvas = $('#pdf-canvas-' + i);
                                                if (canvas.length) {
                                                    var canvasTop = canvas.offset().top - container.offset().top + container.scrollTop();
                                                    if (scrollTop >= canvasTop - 100) {
                                                        foundPage = i;
                                                    }
                                                }
                                            }
                                            if (foundPage !== currentPage) {
                                                currentPage = foundPage;
                                                $('#pdf-page-info').text('Page ' + currentPage + ' of ' + pdf.numPages);
                                            }
                                        });
                                        
                                        $('#pdf-prev', container).on('click', function() {
                                            if (currentPage > 1) {
                                                scrollToPage(currentPage - 1);
                                            }
                                        });
                                        
                                        $('#pdf-next', container).on('click', function() {
                                            if (currentPage < pdf.numPages) {
                                                scrollToPage(currentPage + 1);
                                            }
                                        });
                                        
                                        var reRenderPDF = function(scaleMultiplier) {
                                            currentScale = Math.max(0.5, Math.min(scaleMultiplier, 3.0)); // Clamp between 0.5x and 3x
                                            var zoomLoading = $('<div style="text-align: center; padding: 20px; background: rgba(0,0,0,0.5); color: white; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; border-radius: 4px;"><i class="fa fa-spinner fa-spin"></i> Rendering...</div>');
                                            container.css('position', 'relative').append(zoomLoading);
                                            canvasContainer.empty();
                                            
                                            // Re-render all pages
                                            var renderPageZoom = function(num) {
                                                pdf.getPage(num).then(function(page) {
                                                    // Calculate scale to fit container width with multiplier
                                                    var viewport = page.getViewport({scale: 1.0});
                                                    var pageWidth = viewport.width;
                                                    var calculatedScale = containerWidth / pageWidth;
                                                    var scale = Math.min(calculatedScale * currentScale, 3.0);
                                                    
                                                    var scaledViewport = page.getViewport({scale: scale});
                                                    var canvas = $('<canvas></canvas>').attr({
                                                        id: 'pdf-canvas-' + num
                                                    })[0];
                                                    var context = canvas.getContext('2d');
                                                    canvas.height = scaledViewport.height;
                                                    canvas.width = scaledViewport.width;
                                                    
                                                    // Ensure canvas doesn't exceed container width
                                                    if (canvas.width > containerWidth) {
                                                        var scaleRatio = containerWidth / canvas.width;
                                                        canvas.style.width = containerWidth + 'px';
                                                        canvas.style.height = (canvas.height * scaleRatio) + 'px';
                                                    } else {
                                                        canvas.style.width = canvas.width + 'px';
                                                        canvas.style.height = canvas.height + 'px';
                                                    }
                                                    
                                                    var pageDiv = $('<div style="margin-bottom: 20px; width: 100%; text-align: center;"></div>');
                                                    pageDiv.append(canvas);
                                                    canvasContainer.append(pageDiv);
                                                    
                                                    var renderContext = {
                                                        canvasContext: context,
                                                        viewport: scaledViewport
                                                    };
                                                    
                                                    page.render(renderContext).promise.then(function() {
                                                        if (num < pdf.numPages) {
                                                            renderPageZoom(num + 1);
                                                        } else {
                                                            zoomLoading.remove();
                                                            scrollToPage(currentPage);
                                                        }
                                                    });
                                                });
                                            };
                                            renderPageZoom(1);
                                        };
                                        
                                        $('#pdf-zoom-in', container).on('click', function() {
                                            reRenderPDF(currentScale + 0.25);
                                        });
                                        
                                        $('#pdf-zoom-out', container).on('click', function() {
                                            reRenderPDF(currentScale - 0.25);
                                        });
                                    }
                                    
                                    // Hide loading and show PDF
                                    loadingIndicator.fadeOut(300, function() {
                                        $(this).remove();
                                        iframe_box.append(container);
                                        container.fadeIn(300);
                                    });
                                }
                            });
                        });
                    };
                    
                    renderPage(1);
                }).catch(function(error) {
                    console.error('Error loading PDF:', error);
                    loadingIndicator.html('<h3 class="text-danger"><i class="fa fa-exclamation-circle"></i> Error loading PDF</h3>' +
                        '<p class="text-muted">' + error.message + '</p>');
                    fallbackToIframe();
                });
            };
            reader.readAsArrayBuffer(pdfBlob);
        };

        // Fallback to iframe if PDF.js fails
        var fallbackToIframe = function() {
            loadingIndicator.html('<h3><i class="fa fa-spinner fa-spin fa-pulse text-info"></i> Loading document preview...</h3>' +
                '<p class="text-muted">Please wait while the document is being generated.</p>');
            
            var new_ifrm = $('<iframe></iframe>').attr({
                id: 'iframe_doc_preview',
                src: 'about:blank',
                style: 'width:100%; height:75vh; display: none;'
            });

            iframe_box.children('#iframe_doc_preview').remove();
            iframe_box.append(new_ifrm);
            
            var ifrm = $('#iframe_doc_preview',document);
            var formSubmitted = false;
            
            ifrm.on('load', function() {
                if (!formSubmitted) {
                    formSubmitted = true;
                    try {
                        var body = ifrm.contents().find('body');
                        if (body.length > 0) {
                            var form = $('<form></form>').attr({
                                method: 'post',
                                action: PECO.base_url() + 'cad/getdocumentpreview'
                            });

                            form.append($('<input>').attr({type: 'hidden', name: 'id', value: dataid}));
                            form.append($('<input>').attr({type: 'hidden', name: 'doctype', value: iframe_box.attr('data-id')}));

                            if (params && typeof params === 'object') {
                                $.each(params, function(index, value) {
                                    form.append($('<input>').attr({type: 'hidden', name: index, value: value}));
                                });
                            }

                            body.append(form);
                            form.submit();
                        }
                    } catch(e) {
                        console.error('Error accessing iframe contents:', e);
                        loadingIndicator.html('<h3 class="text-danger"><i class="fa fa-exclamation-circle"></i> Error loading document</h3>');
                    }
                } else {
                    loadingIndicator.fadeOut(300, function() {
                        $(this).remove();
                        ifrm.fadeIn(300);
                    });
                }
            });
        };

        // Fetch PDF as blob
        fetch(PECO.base_url() + 'cad/getdocumentpreview', {
            method: 'POST',
            body: formData
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        })
        .then(function(blob) {
            if (blob.type === 'application/pdf' || blob.type === '') {
                loadAndRenderPDF(blob);
            } else {
                // If not PDF, fallback to iframe
                fallbackToIframe();
            }
        })
        .catch(function(error) {
            console.error('Error fetching PDF:', error);
            loadingIndicator.html('<h3 class="text-danger"><i class="fa fa-exclamation-circle"></i> Error loading document</h3>' +
                '<p class="text-muted">' + error.message + '</p>');
            // Fallback to iframe after a delay
            setTimeout(fallbackToIframe, 2000);
        });
    };

    var contract_handler = function (dataid) {
        var frm_application_missing_details = $('#frm_application_missing_details',document);
        //On Change system Size, populate Installment Plan.
        PECO.select2Basic($('#select2_systemsize',document),'inspection/select2systemsize','Select System Size...',false,false,false);
        PECO.select2Basic($('#select2_plantype',document),'cad/select2planduration','Select Plan Duration...',false,false,false);
        PECO.select2Basic($('#select_billdate',document),'billing/select2billingdate','Billing date...',false,false,$('#select_billdate',document).val());
        PECO.select2Basic($('#select2_billingstart',document),'systems/select2month','Select start of billing series...',false,false,false);

        var billingstart = $('#select2_billingstart',document).val();
        var param = {};
        if (billingstart !== '') {
            param['billingstart'] = billingstart;
        }
        create_docs_preview(dataid,param);
        frm_application_missing_details.find('#systemtype_row .icheck-inline .icheck').each(function(){
            var this_ = $(this);
            if (this_.is(':checked')) {
                var target = this_.attr('data-target');
                if ($(target,document).hasClass('hidden')) {
                    $(target,document).removeClass('hidden');
                }
                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',false);
                });
                if (this_.val() == 1) {
                    $('#systemprice',document).attr('disabled',true);
                } else {
                    $('#systemprice',document).attr('disabled',false);
                }
            } else {
                var target = this_.attr('data-target');
                $(target,document).addClass('hidden');
                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',true);
                });
            }

            this_.on('ifChecked', function(){
                this_.attr('checked', true);
                var target = this_.attr('data-target');

                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',false);
                });
                if ($(target,document).hasClass('hidden')) {
                    $(target,document).removeClass('hidden');
                }

                if (this_.val() == 1) {
                    $('#systemprice',document).attr('disabled',true);
                } else {
                    $('#systemprice',document).attr('disabled',false);
                }
                //$(target,document).attr('disabled',false);
                //alert($(this).attr('data-target'));
            }).on('ifUnchecked', function(){
                this_.attr('checked', false);
                var target = this_.attr('data-target');
                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',true);
                });
                $(target,document).addClass('hidden');
            });
        });

        frm_application_missing_details.on('submit',function (e) {
            var this_ = $(this);
            e.preventDefault();
            $.ajax({
                url : this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data : this_.serialize()
            }).done(function (d) {
                if (d.alerts.length > 0) {
                    var alert = d.alerts;
                    for (var i = 0; i < d.alerts.length; i++) {
                        PECO.initAlerts(alert[i]['msg'],alert[i]['title'],alert[i]['func']);
                    }
                }
                create_docs_preview(dataid);
            }).fail(function () {
                PECO.initAlerts('PHP ERROR!!!','ERROR!','error');
            });
        });

        $('#btn_reload_preview',document).on('click',function () {
            var billingstart = $('#select2_billingstart',document).val();
            var param = {};
            if (billingstart !== '') {
                param['billingstart'] = billingstart;
            }
            create_docs_preview(dataid,param);
        });

        $('#select2_billingstart',document).on('change',function () {
            var this_ = $(this);
            var billingstart = this_.val();
            var param = {};
            if (billingstart !== '') {
                param['billingstart'] = billingstart;
            }
            create_docs_preview(dataid,param);
        });

        $('#btn_open_preview',document).on('click',function () {
            var doctype = iframe_box.attr('data-id');
            var billingstart = $('#select2_billingstart',document).val();
            $.ajax({
                url: PECO.base_url() + 'cad/getdocumentlayout',
                type: 'post',
                dataType: 'json',
                data: {
                    id: dataid,
                    doctype: doctype,
                    billingstart: billingstart
                }
            }).done(function (d) {
                PECO.pdfPreview(d.title,d.html,d.papersize);
            }).fail(function () {

            });
        });
        //$('.icheck-inline .icheck', $('#frm_application_missing_details',document))

        $('#btn_finalize_contract',document).on('click',function () {
            var billingstart = $('#select2_billingstart',document).val();
            $.ajax({
                url : PECO.base_url() + 'cad/finalizedocument',
                type: 'post',
                dataType: 'json',
                data: {
                    id : dataid,
                    finalize : true,
                    doctype : 3434,
                    billingstart: billingstart
                }
            }).done(function (d) {
                if (d.msg.length > 0) {
                    PECO.initAlerts(d.msg,'Finalize Document',d.func);
                }
                if (d.qry) {
                    if (!$('#generate_contract',document).hasClass('hidden')) {
                        $('#generate_contract', document).addClass('hidden');
                    }
                    if ($('#delete_contract',document).hasClass('hidden')) {
                        $('#delete_contract',document).removeClass('hidden');
                    }

                    create_billing_sequence(dataid);

                    create_docs_preview(dataid);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#btn_regenerate_document',function () {
            var doctype = iframe_box.attr('data-id');
            $.ajax({
                url: PECO.base_url() + 'cad/deletedocument',
                type: 'post',
                dataType: 'json',
                data: {
                    id: dataid,
                    doctype: doctype,
                }
            }).done(function (d) {
                if (d.qry) {
                    btn_finalize_contract(dataid);
                }
                PECO.initAlerts(d.msg, 'New Proposal', d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('submit','#frm_newaccount',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data : this_.serialize()
            }).done(function (d) {
                //var win = window.open('','');
                //win.document.write(JSON.stringify(d));
                PECO.pdfPreview(d.title,d.html,d.papersize);
            }).fail(function () {

            })
        });
    };

    var btn_finalize_contract = function(dataid) {
        $.ajax({
            url : PECO.base_url() + 'cad/finalizedocument',
            type: 'post',
            dataType: 'json',
            data: {
                id : dataid,
                doctype : 3434
            }
        }).done(function (d) {
            if (d.docid) {
                if (!$('#generate_contract',document).hasClass('hidden')) {
                    $('#generate_contract', document).addClass('hidden');
                    $('#select2_billingstart', document).attr('disabled',true);
                }
                if ($('#delete_contract',document).hasClass('hidden')) {
                    $('#delete_contract',document).removeClass('hidden');
                }
            } else {
                if (!$('#delete_contract',document).hasClass('hidden')) {
                    $('#delete_contract', document).addClass('hidden');
                }
                if ($('#generate_contract',document).hasClass('hidden')) {
                    $('#generate_contract',document).removeClass('hidden');
                    $('#select2_billingstart',document).attr('disabled',false);
                }
            }

            $(document).find('#select2_billingstart').each(function () {
                var this_ = $(this);
                PECO.select2Basic(this_,'systems/select2month','Select start of billing series...',false,false,false);
            });
        }).fail(function () {
            PECO.phpError();
        });
    };

    var installment_event = function () {
        var frm_application_missing_details = $('#frm_application_missing_details',document);
        if (($('#standardtype',document).is(':checked') && $('#select2_systemsize',document).val()) || ($('#nonstandardtype',document).is(':checked') && $('#systemprice',document).val() !== false)) {
            frm_application_missing_details.find('#installmentplan .icheck-inline .icheck').each(function () {
                $(this).attr('disabled',false);
            })
        } else {
            frm_application_missing_details.find('#installmentplan .icheck-inline .icheck').each(function () {
                $(this).attr('disabled',true);
            })
        }
    };

    var create_billing_sequence = function (appid) {
        var billingstart = $('#select2_billingstart',document).val();
        $.ajax({
            url: PECO.base_url() + 'billing/createbillingsequence',
            type: 'post',
            dataType: 'json',
            data: {
                id : appid,
                billingstart: billingstart
            }
        }).done(function (d) {
            PECO.initAlerts(d.msg,d.title,d.func);
        }).fail(function () {
           PECO.phpError();
        });
    };

    var system_rates_handler = function () {
        dt_system_rates();
        var tbl_data = {};

        var tbl_sysrates = $('#tbl_sysrates',document);

        $(document).on('click','#btn_reload_sysrates',function () {
            dt_system_rates();
        });

        tbl_sysrates.on('click','#btn_edit_system_size',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_td = this_.closest('td');

            var rowIndex = this_tr.index();
            tbl_data[rowIndex] = {};
            tbl_data[rowIndex]['controls'] = this_td.html();


            var edit = '';
            edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_update_rate"><i class="fa fa-save"></i> Save</a>';
            edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_cancel_edit"><i class="fa fa-times"></i> Cancel</a>';

            this_td.html(edit);

            this_tr.find('input').each(function () {
                $(this).attr('disabled',false);
                var style = $(this).attr('style');
                $(this).attr('style',style + 'border-bottom: 1px solid #333333 !important');
                $(this).val( $(this).val().replace(/,/g, "") );
                tbl_data[rowIndex][$(this).attr('name')] = $(this).val();
            });
            console.log(tbl_data);
        });

        tbl_sysrates.on('click','#btn_cancel_edit, #btn_update_rate',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var target = this_.attr('href');

            var this_tr = this_.closest('tr');
            var this_td = this_.closest('td');
            var rowIndex = this_tr.index();
            var sizeid = $('#sizeid',this_tr).val();

            if (target === '#save') {
                var values = tbl_data[rowIndex];
                var changes = {};
                this_tr.find('input').each(function () {
                    var val = $(this).val();
                    if (val !== values[$(this).attr('name')]) {
                        var year = $(this).attr('data-years');
                        changes[year] = val;
                    }
                });
                if (Object.keys(changes).length > 0) {
                    swal({
                        title: "Save current changes?",
                        text: 'This will replace the current rates of the selected system size.',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-info",
                        confirmButtonText: "Yes!",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        showLoaderOnConfirm: true
                    }, function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: PECO.base_url() + 'billing/updatesystemrates',
                                type: 'post',
                                dataType: 'json',
                                data: {
                                    sizeid : sizeid,
                                    values : changes
                                }
                            }).done(function (d) {
                                swal(d.title,d.msg,d.func);
                                //swal('test','test','success');
                                if (d.qry) {
                                    dt_system_rates();
                                    delete tbl_data[rowIndex];
                                }
                            }).fail(function () {
                                swal('Fail!','Failed to update system rates.','error');
                            })
                        }
                    });
                } else {
                    swal('No Changes','There were no changes in any of the rates.','info');
                }
            }

            if (target === '#cancel') {
                var values = tbl_data[rowIndex];
                this_tr.find('input').each(function () {
                    $(this).val(values[$(this).attr('name')]);
                    $(this).css('border-bottom','');
                    $(this).attr('disabled',true);
                    PECO.formatNumber($(this),true);
                });
                this_td.html(tbl_data[rowIndex]['controls']);
                delete tbl_data[rowIndex];
            }
        });

    };

    var dt_system_rates = function () {
        var tbl_sysrates = $('#tbl_sysrates',document);
        $.ajax({
            url: PECO.base_url() + 'billing/dtgetsystemrates',
            type: 'post',
            dataType: 'json',
            data : {},
            beforeSend: function() {
                PECO.DTphpLoading(tbl_sysrates, 'Loading System Size Rates...');
            }
        }).done(function (d) {
            tbl_sysrates.DataTable({
                bDestroy: true,
                info: true,
                scrollCollapse: true,
                paging: false,
                saveState: true,
                searchHighlight: true,
                aoColumns: d.columns,
                bStateSave: true,
                bProcessing: true,
                aaData:d.list
            });
        }).fail(function () {

        });
    };

    var proposal_handler = function () {
        PECO.select2Basic($('#select2_systemsize',document),'inspection/select2systemsize','Select System Size...',false,false,false);
        var frm_newaccount = $('#frm_newaccount',document);

        $('#frm_application_missing_details',document).find('#systemtype_row .icheck-inline .icheck').each(function(){
            var this_ = $(this);
            if (this_.is(':checked')) {
                var target = this_.attr('data-target');
                if ($(target,document).hasClass('hidden')) {
                    $(target,document).removeClass('hidden');
                }
                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',false);
                });
                if (this_.val() == 1) {
                    $('#systemprice',document).attr('disabled',true);
                } else {
                    $('#systemprice',document).attr('disabled',false);
                }
            } else {
                var target = this_.attr('data-target');
                $(target,document).addClass('hidden');
                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',true);
                });
            }

            this_.on('ifChecked', function(){
                this_.attr('checked', true);
                var target = this_.attr('data-target');

                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',false);
                });
                if ($(target,document).hasClass('hidden')) {
                    $(target,document).removeClass('hidden');
                }

                if (this_.val() == 1) {
                    $('#systemprice',document).attr('disabled',true);
                } else {
                    $('#systemprice',document).attr('disabled',false);
                }
                //$(target,document).attr('disabled',false);
                //alert($(this).attr('data-target'));
            }).on('ifUnchecked', function(){
                this_.attr('checked', false);
                var target = this_.attr('data-target');
                $(target,document).find('input').each(function () {
                    $(this).attr('disabled',true);
                });
                $(target,document).addClass('hidden');
            });
        });

        frm_newaccount.on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var data = new FormData(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data: data,
                cache: false,
                contentType: false,
                processData: false
            }).done(function (d) {
                console.log(d);
                PECO.pdfPreview(d.title,d.html);
            }).fail(function () {
                PECO.phpError();
            });
        });
    };

    var signed_list = function () {
        var tbl_signed_proposals = $('#sales_signed_proposals',document);
        dt_signed_proposals();

        tbl_signed_proposals.on('click','#btn_view_signed_proposal',function () {
            var this_ = $(this);
            var dataid = this_.attr('data-id');
            var sysid = this_.attr('data-sysid');
            var doctype = this_.attr('data-type');
            console.log({ID: dataid, SysID: sysid, DocType: doctype});
            var win = window.open('','');
            win.document.title = 'Customer Application Proposal';

            const form = document.createElement('form');
            form.method = 'post';
            form.action = PECO.base_url() + 'printer/docspreview';

            const idField = document.createElement('input');
            idField.type = 'hidden';
            idField.name = 'id';
            idField.value = dataid;

            form.appendChild(idField);

            const sysidField = document.createElement('input');
            sysidField.type = 'hidden';
            sysidField.name = 'sysid';
            sysidField.value = sysid;

            form.appendChild(sysidField);

            const doctypeField = document.createElement('input');
            doctypeField.type = 'hidden';
            doctypeField.name = 'doctype';
            doctypeField.value = doctype;

            form.appendChild(doctypeField);

            win.document.body.appendChild(form);
            form.submit();
            /*$.ajax({
                url: PECO.base_url() + 'printer/docspreview',
                type: 'post',
                dataType: 'json',
                data: {
                    id: dataid,
                    doctype: 3433,
                    print : true
                }
            }).done(function (d) {
                PECO.pdfPreview(d.title,d.html,d.papersize);
            }).fail(function () {

            });*/
        });
    };

    var dt_signed_proposals = function () {
        var tbl_signed_proposals = $('#sales_signed_proposals',document);
        $.ajax({
            url : PECO.base_url() + 'cad/getsignedproposallist',
            type : 'post',
            dataType : 'json',
        }).done(function (d) {
            tbl_signed_proposals.DataTable({
                bDestroy: true,
                info: true,
                scrollCollapse: true,
                paging: false,
                saveState: true,
                searchHighlight: true,
                aoColumns: d.columns,
                bStateSave: true,
                bProcessing: true,
                aaData:d.list,
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> No signed proposals.'
                },
            });
        }).fail(function () {

        });
    };

    var viewer_handler = function (dataid) {
        var doc_preview_box = $('#doc_preview_box', document);
        var doc_preview_tabs = $('#doc_preview_tabs', doc_preview_box);
        var tab = $('a[data-toggle="tab"]',doc_preview_tabs);
        tab.on('shown.bs.tab',function (e) {
            var target = $(e.target).attr('href');
            var id = $(e.target).attr('data-id');
            var elem = target.split('_');
            if (elem[1] !== 'others') {
                console.log('elem = ' + elem[1]);
                finalize_viewer_document(elem[1], dataid, id);
            }
        });

        $(document).on('click','#btn_finalize_viewer_document',function () {
            var active_tab = $('.active a[data-toggle="tab"]',doc_preview_tabs);
            var target = active_tab.attr('href');
            var elem = target.split('_');
            var id = active_tab.attr('data-id');
            finalize_viewer_document(elem[1], dataid, id,true);
        });

        $(document).on('click','#btn_finalize_viewer_contract',function () {
            var active_tab = $('.active a[data-toggle="tab"]',doc_preview_tabs);
            var target = active_tab.attr('href');
            var elem = target.split('_');
            var id = active_tab.attr('data-id');
            create_billing_sequence(dataid);
            finalize_viewer_document(elem[1], dataid, id,true);
        });

        $(document).on('click','#btn_regenerate_viewer_document',function () {
            var active_tab = $('.active a[data-toggle="tab"]',doc_preview_tabs);
            var target = active_tab.attr('href');
            var elem = target.split('_');
            var id = active_tab.attr('data-id');
            $.ajax({
                url : PECO.base_url() + 'cad/deletedocument',
                type: 'post',
                dataType: 'json',
                data: {
                    id : dataid,
                    doctype : id,
                }
            }).done(function (d) {
                if (d.qry) {
                    finalize_viewer_document(elem[1], dataid, id);
                }
                PECO.initAlerts(d.msg,'New Proposal',d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#btn_reload_preview',function () {
            var this_ = $(this);
            var active_tab = $('.active a[data-toggle="tab"]',doc_preview_tabs);
            var target = active_tab.attr('href');
            var elem = target.split('_');
            var id = active_tab.attr('data-id');
            ATTACHEMENTS.iframePreview(elem[1], dataid, id);
            finalize_viewer_document(elem[1], dataid, id);
        });
    }

    var finalize_viewer_document = function (elem,dataid,doctype,finalize) {
        console.log('doctype : ' + doctype);
        var tab = $('#doc_' + elem,document);
        tab.find('#preview_actions').remove();

        if (elem !== 'tssr') {
            tab.append('<div id="preview_actions"></div>');
            $.ajax({
                url: PECO.base_url() + 'cad/finalizedocument',
                type: 'post',
                dataType: 'json',
                data: {
                    id: dataid,
                    finalize: finalize,
                    doctype: doctype,
                    viewer: true
                }
            }).done(function (d) {
                if (d.msg.length > 0) {
                    PECO.initAlerts(d.msg, 'Finalize Document', d.func);

                }
                $('#preview_actions', tab).html(d.buttons);
                if (elem === 'contract') {
                    $(document).find('#select2_billingstart').each(function () {
                        var this_ = $(this);
                        PECO.select2Basic(this_,'systems/select2month','Select start of billing series...',false,false,false);
                    });
                }
            }).fail(function () {
                PECO.phpError();
            });
        }
    };

    return {
        init: function (dataid) {
            init_sales(dataid);
            sales_handler(dataid);
            create_docs_preview(dataid);
        },
        contract: function (dataid) {
            contract_handler(dataid);
            if (dataid) {
                btn_finalize_contract(dataid);
            }
        },
        rates: function () {
            system_rates_handler();
        },
        generator: function () {
            proposal_handler();
            //contract_handler();
        },
        proposals: function () {
            signed_list();
        },
        viewer: function (dataid) {
            //init_sales(dataid);
            viewer_handler(dataid);
            sales_handler(dataid);
            contract_handler(dataid);
        }

    }
}();