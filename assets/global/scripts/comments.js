var COMM = function () {
    var convo = [];
    var comments_section = $('#comments_section',document);
    var frm_new_comment = $('#frm_new_comment',document);

    var init_comments = function (types,moduleid,dataid,stageid) {
        populate_comments_convo(types,moduleid,dataid,stageid);
        comments_section.scrollTop(comments_section.prop('scrollHeight'));

        frm_new_comment.on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_btn = this_.children('button');
            var init_btn = this_btn.html();

            $.ajax({
                url : this_.attr('action'),
                type : this_.attr('method'),
                dataType : 'json',
                data : this_.serialize(),
                beforeSend: function () {
                    this_btn.html('<i class="fa fa-spinner fa-spin fa-pulse"></i> Sending...');
                }
            }).done(function (d) {
                if (d.qry) {
                    comments_section.find('#no_comment').remove();
                    this_btn.html('<i class="fa fa-check"></i> Sent!');
                    this_btn.removeClass('btn-primary').addClass('btn-success');
                    comments_section.append(d.newcomment);
                    this_.trigger('reset');
                    comments_section.scrollTop(comments_section.prop('scrollHeight'));
                    convo.push(d.comment);
                    console.log(convo);
                } else {
                    this_btn.html('<i class="fa fa-times"></i> Fail!');
                    this_btn.removeClass('btn-primary').addClass('btn-danger');
                }
                setTimeout(function () {
                    this_btn.html(init_btn);
                    this_btn.removeClass('btn-danger').removeClass('btn-success').addClass('btn-primary');
                },1000);

                $('#reply_quote',this_).text('');
                $('#quoted_id',this_).val('');
            }).fail(function () {
                this_btn.html('<i class="fa fa-times"></i> Fail!');
                this_btn.removeClass('btn-primary').addClass('btn-danger');
                setTimeout(function () {
                    this_btn.html(init_btn);
                    this_btn.removeClass('btn-danger').addClass('btn-primary');
                },1000);
            });
        });

        frm_new_comment.on('keyup','#comment_area',function () {
            var this_ = $(this);
            var this_maxlenght = this_.attr('maxlength');
            var characterCount = this_.val().length;
            var current_count = $('#current_count',frm_new_comment);
            var max_count = $('#max_count',frm_new_comment);

            current_count.text(characterCount);

            if (characterCount <= 120) {
                current_count.css('color', '#ffffff');
                max_count.css('color', '#ffffff');
            }
            if (characterCount > 120 && characterCount < 140) {
                current_count.css('color', '#ffff00');
                max_count.css('color', '#ffff00');
            }
            if (characterCount >= 140) {
                current_count.css('color', '#ff0000');
                max_count.css('color', '#ff0000');
            }

            /*if (characterCount >= 140) {
                maximum.css('color', '#8f0001');
                current.css('color', '#8f0001');
                theCount.css('font-weight','bold');
            } else {
                maximum.css('color','#666');
                theCount.css('font-weight','normal');
            }*/
        });

        comments_section.on('click','#delete_comment',function () {
            var this_ = $(this);
            var id = this_.attr('data-id');
            var this_comment = this_.closest('.comment-content');
            var comment = this_comment.find('p').text();
            var comment_row = this_comment.closest('#comment_row');
            var comments_section = $('#comments_section',document);

            if (confirm('You are about to delete this comment:\n\n"'+comment+'"') === true) {
                $.ajax({
                    url : PECO.base_url() + 'admin/deletetrncomment',
                    type : 'post',
                    dataType : 'json',
                    data : {
                        commentid : id
                    }
                }).done(function (d) {
                    if (d.qry) {
                        comment_row.remove();
                        alert('Comment has been deleted.');
                        if (comments_section.children().length === 0) {
                            comments_section.html('<h4 id="no_comment"><i class="fa fa-warning text-warning"></i> No comments posted!</h4>');
                        }
                    } else {
                        alert('Unable to delete comment.');
                    }
                }).fail(function () {
                    alert('PHP error!!!');
                });
            }
        });

        comments_section.on('mouseenter','#comment_row',function () {
            var this_ = $(this);
            $('#reply_btn',this_).removeClass('hidden');
        }).on('mouseleave','#comment_row',function () {
            var this_ = $(this);
            $('#reply_btn',this_).addClass('hidden');
        })

        comments_section.on('click','#reply_btn',function () {
            var this_ = $(this);
            var comment_id = $('#reply_comment',this_).attr('data-id');
            var comment_row = this_.closest('#comment_row');
            var commenter = $('#commenter',comment_row).text();
            var comment_content = $('.comment-content p',comment_row).text();

            var reply_quote = $('#reply_quote',frm_new_comment);
            var quoted_id = $('#quoted_id',frm_new_comment);
            reply_quote.html('Replying to ' + commenter + ' : "' + comment_content + '" <i class="fa fa-times text-danger pull-right" id="remove_quote_comment"></i>');
            quoted_id.val(comment_id);
        });

        frm_new_comment.on('click','#remove_quote_comment',function () {
            var reply_quote = $('#reply_quote',frm_new_comment);
            var quoted_id = $('#quoted_id',frm_new_comment);
            reply_quote.html('');
            quoted_id.val('');
        })

        comments_section.on('click','.quoted-content',function () {
            var this_ = $(this);
            var comment_id = this_.attr('href');

            var comment = $(comment_id,document);

            comments_section.scrollTop(comments_section.scrollTop() + comment.position().top);
        });

        /*setInterval(function () {
            fetch_new_comment(types,moduleid,dataid,stageid);
        },5000);*/

        fetchComments = setInterval(function(){ fetch_new_comment(types,moduleid,dataid,stageid) }, 5000);
    };

    var populate_comments_convo = function (types,moduleid,dataid,stageid) {
        //console.log(arguments);
        $.ajax({
            url : PECO.base_url() + 'admin/gettrncomments',
            type : 'post',
            dataType : 'json',
            data : {
                types : types,
                moduleid : moduleid,
                dataid : dataid,
                stageid : stageid
            },
            beforeSend: function () {
                comments_section.html('<h4 id="no_comment"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Fetching comments.</h4>')
            }
        }).done(function (d) {
            if (d.comments !== undefined && d.comments.length > 0) {
                comments_section.find('#no_comment').remove();
                $.each(d.comments,function (index,html) {
                    comments_section.append(html);
                });
                $.each(d.commentlogs,function (index,comments) {
                    convo.push(comments);
                })
            } else {
                comments_section.html('<h4 id="no_comment"><i class="fa fa-warning text-warning"></i> No comments posted!</h4>');
            }
            comments_section.scrollTop(comments_section.prop('scrollHeight'));
        }).fail(function () {
            comments_section.html('<h4 id="no_comment"><i class="fa fa-warning text-danger"></i> PHP Error!</h4>');
        });
        console.log(convo);
    };

    var fetch_new_comment = function (types,moduleid,dataid,stageid) {
        //var comment_result = [];
        var sysid = [];
        convo.forEach(function (v) {
            sysid.push({
                source : v.source,
                sysid : v.sysid
            });
        });

        $.ajax({
            url : PECO.base_url() + 'admin/gettrncomments',
            type : 'post',
            dataType : 'json',
            data : {
                types : types,
                moduleid : moduleid,
                dataid : dataid,
                stageid : stageid,
                sysid : sysid
            }
        }).done(function (d) {
            if (d.comments !== undefined && d.comments.length > 0) {
                comments_section.find('#no_comment').remove();
                $.each(d.comments,function (index,html) {
                    comments_section.append(html);
                });
                $.each(d.commentlogs,function (index,comments) {
                    convo.push(comments);
                });
            }
        }).fail(function () {

        });
    };

    var viewer_comments = function (types,moduleid,dataid,stageid) {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr("href");

            var init = false;

            if (target === '#comment_view') {
                init_comments(types,moduleid,dataid,stageid);
            } else {
                clearInterval(window.fetchComments);
                frm_new_comment.off('submit');
            }
        });
    }

    return {
        init : function (types,moduleid,dataid,stageid) {
            init_comments(types,moduleid,dataid,stageid);
        },
        viewer : function (types,moduleid,dataid,stageid) {
            viewer_comments(types,moduleid,dataid,stageid);
        }
    }
}();