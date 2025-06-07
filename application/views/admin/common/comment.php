<?php
$comment_qry = $this->db->select('sysid,userid,subjects,messages,datecreated')
    ->from('comments')
    ->where(array(
            'types' => $types,
            'moduleid' => $moduleid,
            'dataid' => $dataid,
            'stageid' => $stageid,
            'status' => 1
    ))->get();

?>

<style type="text/css">
    .comment-content {
        border-radius: 5px 15px 15px 15px !important;
        padding: 5px;
        min-width: 200px;
        max-width: 400px;
        min-height: 30px;
        /*line-height: 25px;*/
    }

    .comment-you {
        /*padding-left: 25px !important;*/
        float: right !important;
    }

    .comment-you .comment-content {
        background-color: rgba(10, 182, 255, 0.5);
    }

    .comment-them .comment-content {
        outline-style: solid;
        outline-width: 2px;
        outline-offset: -2px;
        outline-color: rgba(117, 114, 114, 0.5);
        background-color: transparent;
    }

    .comment-content p {
        margin: 0px !important;
    }

    #comments_section {
        overflow-x: hidden;
        overflow-y: auto;

    }
</style>

<div class="row">
    <div class="col-md-8" id="comments_section" style="max-height: 190px !important;min-height: 185px !important;">

    </div>
    <div class="col-md-4" style="max-height: 195px !important;min-height: 195px !important;">
        <form id="frm_new_comment" method="post" action="<?php echo base_url('admin/addtrncomment');?>">
            <h4 class="bold"> Post a comment</h4>
            <div class="row margin-top-10">
                <input type="hidden" name="types" value="<?php echo $types;?>">
                <input type="hidden" name="moduleid" value="<?php echo $moduleid;?>">
                <input type="hidden" name="dataid" value="<?php echo $dataid;?>">
                <input type="hidden" name="stageid" value="<?php echo $stageid;?>">
                <div class="col-md-12">
                    <textarea id="comment_area" class="form-control" rows="3" name="messages" style="width: 100% !important; padding-bottom: 20px" placeholder="What do you want to say?" maxlength="150" required></textarea>
                    <div id="comment_count" class="col-md-2 bg-blue pull-right text-align-center" style="margin: -20px 0px; padding: 0px 2px; color: white">
                        <span class="small" id="current_count">0</span>/<span class="small" id="max_count">150</span>
                    </div>
                </div>
                <div class="col-md-3 pull-right margin-top-20">
                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-send"></i> Post</button>
                </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url()?>assets/global/scripts/comments.js"></script>

<script type="text/javascript">
    COMM.init(<?php echo $types;?>,<?php echo $moduleid;?>,<?php echo $dataid;?>,<?php echo $stageid;?>);
</script>