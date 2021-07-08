</div>
<footer class="mt-4 back_g">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="mt-3 pb-3">
                    <?php require($CFG->dir_com.'/SocialBtns/SocialBtns.php');?>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mt-3 pb-3">
                    <i class="fa fa-map-marker"></i> <?=ADDRESS?>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="mt-3 pb-3">
                    <i class="fa fa-at"></i> <?=SITE_MAIL?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bottom_block pt-3 text-center">© <?=date('Y',$_SERVER['REQUEST_TIME']) . ' '. SITE_TITLE?></div>
</footer>
</div><!-- PAGE -->
<script src="<?=SITE_URL?>/js/script.js"></script>
</body>
</html>