<div class="page-content-wrapper" >

    <div class="page-content  animated fadeInUp fast" >
        <?php
        /**
         * Created by PhpStorm.
         * User: DUDEZ
         * Date: 7/21/2018
         * Time: 10:33 AM
         */
        // CHECK IF PASSWORD HAS CHANGED
        $user_info = get_users_info(user_id());

        if($user_info) {
            $check_confirm = $this->db->select()->from('prime_system_users_confirmation')
                ->where(array('personid' => $user_info->pid, 'status' => 2))
                ->get()->row();
            if($check_confirm) {
                redirect(base_url('profile', 'refresh'));
            } else {
                echo '<div class="note note-info note-bordered"><h3>Welcome, '.$user_info->firstname.'!</h3></div>';
            }
        } else {
            page_data_notfound('Account Information is not found!');
        }

        ?>

        <?php


        $user_nav = get_users_info_navigation_ids();

        $nav_array = array();

        $qry_navs = $this->db->select()
            ->from('prime_module_navigations_main AS sn')
            ->where(array('sn.status' => 1, 'sn.levels' => 1))
            ->get();

        if ($qry_navs->num_rows() > 0) {
            foreach ($qry_navs->result() AS $nav) {
                //lookup children with user access
                $children = $this->db->select()
                    ->from('prime_module_navigations_main AS sn')
                    ->where(array('sn.status' => 1, 'sn.parent' => $nav->sysid))
                    ->where_in('sysid',$user_nav)
                    ->order_by('sorting ASC')
                    ->get();


                if ($children->num_rows() > 0) {
                    foreach ($children->result() AS $nav_a) {

                        $children_a = $this->db->select()
                            ->from('prime_module_navigations_main AS sn')
                            ->where(array('sn.status' => 1, 'sn.parent' => $nav_a->sysid))
                            ->where_in('sysid',$user_nav)
                            ->order_by('sorting ASC')
                            ->get();

                        if ($children_a->num_rows() > 0) {
                            foreach ($children_a->result() as $nav_b) {
                                $children_b = $this->db->select()
                                    ->from('prime_module_navigations_main AS sn')
                                    ->where(array('sn.status' => 1, 'sn.parent' => $nav_b->sysid))
                                    ->where_in('sysid',$user_nav)
                                    ->order_by('sorting ASC')
                                    ->get();

                                if ($children_b->num_rows() > 0) {
                                    foreach ($children_b->result() AS $nav_c) {
                                        $children_c = $this->db->select()
                                            ->from('prime_module_navigations_main AS sn')
                                            ->where(array('sn.status' => 1, 'sn.parent' => $nav_c->sysid))
                                            ->where_in('sysid',$user_nav)
                                            ->order_by('sorting ASC')
                                            ->get();

                                        if ($children_c->num_rows() > 0) {
                                            foreach ($children_c->result() AS $nav_d) {
                                                $nav_array[] = $nav_d;
                                            }
                                        } else {
                                            $nav_array[] = $nav_c;
                                        }
                                    }
                                } else {
                                    $nav_array[] = $nav_b;
                                }
                            }
                        } else {
                            $nav_array[] = $nav_a;
                        }
                    }
                }
            }
        }

        ?>

        <?php
        $nav_cnt = count($nav_array);
        if ($nav_cnt > 0) {
            $cnt = 0;
            foreach ($nav_array AS $module) {
                $html = '';
                if (fmod($cnt,4) == 0) {
                    //echo 'Count: '.$cnt;
                    echo '<div class="row margin-bottom-20">';
                }
                $link_action = ($module->type > 2) ? 'target="_blank "' : '';
                $qry_session_module = $this->db->select()->from('prime_module_users_logs')
                    ->where(array('moduleid' => $module->sysid, 'userid' => user_id()))
                    ->get()->row();

                $link = base_url('module/' . $module->hashcode . '/' . $module->url);
                ?>
                <div class="col-lg-3" id="shortcut_item" style="">
                    <a <?php echo $link_action; ?> href="<?php echo $link; ?>" class="inline pull-right " style="white-space: normal; width: 100%" >
                        <div class="mt-element-ribbon bg-grey-steel" style="margin-bottom: unset !important;">
                            <div class="row">
                                <div class="col-md-4">
                                    <i class="fa fa-5x <?php echo $module->icon; ?> text-<?php echo $module->htmlclass; ?> fa-fw" style="line-height: inherit !important;"></i>

                                </div>
                                <div class="col-md-8">
                                    <div class="ribbon ribbon-right ribbon-shadow ribbon-color-success uppercase">
                                        <?php echo $module->desc; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php
                $cnt++;
                if ($cnt >= 4 && fmod($cnt,4) == 0) {
                    echo '</div>';
                }
            }
        } ?>
    </div>
</div>
