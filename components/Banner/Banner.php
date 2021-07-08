<?php /////////////////////////////////////////BANNER//////////////////////////////////////////////////////////////
//require_once ('../../lib/setup.php');
$query_ban ="SELECT * FROM ".$CFG->db['prefix']."banners WHERE visible = 1 ORDER BY position ASC";
$table_ban = $DB->get_records_sql($query_ban, true);
$banners = count($table_ban);

if ($banners >=1 ){?>
    <div class="top-banner">
        <div id="carouselExampleFade" class="carousel slide carousel-fade" data-ride="carousel">
            <div class="carousel-inner">
                <?php
                $i = 0;
                foreach($table_ban as $ban){?>
                    <div class="carousel-item <?php if($i == 0) echo 'active'?>">
                        <?php if($ban['link']){ echo '<a href="'.$ban['link'].'">';?>
                            <img src="img/banners/<?php echo $ban['img']; ?>.jpg" alt="<?php echo SITE_TITLE; ?>">
                            <?php echo '</a>'; }else{ ?>
                            <img src="img/banners/<?php echo $ban['img']; ?>.jpg" alt="<?php echo SITE_TITLE; ?>">
                        <?php } ?>
                    </div>
                    <?php $i++;
                    //dpr($ban);
                } ?>
            </div>
            <?php if ($banners > 1 ){ ?>
                <a class="carousel-control-prev" href="#carouselExampleFade" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleFade" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            <?php } ?>
        </div>
    </div>
<?php } ?>