<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>상세 검색</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=0.5, maximum-scale=1, minimum-scale=0.5, user-scalable=yes, target-desitydpi=device-dpi" />
    <script type="text/javascript" src="contents/js/html5shiv.js"></script>
	<link rel="stylesheet" href="http://fonts.googleapis.com/earlyaccess/nanumgothic.css" />
    <link href="contents/css/reset.css" rel="stylesheet" type="text/css" />
    <link href="contents/css/common.css" rel="stylesheet" />
</head>

<body>
    <form id="form1" runat="server" method="post" enctype="multipart/form-data">
        <header id="header">
    		<h1>
		    	<a href="Search.php?wa="><img src="../../contents/img/common_logo.png" alt="MAM 검색 서비스"/></a>
	    	</h1>
		    <div class="search_wrap">
			    <div>
		            <div class="search_btn_wrap">
<?php include 'VideoEngine.php';

 $searchResults = null;

 if($_SERVER["REQUEST_METHOD"] == "POST"){
 $imgfilepath = $imgtype="";
 $uploaddir = getcwd().'/upfile/';

 if (is_dir($uploaddir) && is_writable($uploaddir)) {
      $videofilepath = $uploaddir.$_FILES['videofile']['name'];
      $videotype = $_FILES['videofile']['type'];
      if(move_uploaded_file($_FILES['videofile']['tmp_name'], $videofilepath)){
        //        print_r($_FILES);
        echo "\n success!<br>";

		$searchResults = videoSearch($_FILES['videofile']['name']);
        }
        else{
                echo "\nfile upload failed<br>";
        }
 } else {

    echo "Upload directory is not writable, or does not exist.<br>";
 }
}

?>
<form name="form" method="POST" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  enctype="multipart/form-data"/>
<input type="hidden" name="MAX_FILE_SIZE" value="1024000000"/>
Video <input type="file" name="videofile"/><br>
<br>
<input type="submit" value="upload"/>
<input type="reset" value="reset"/>
</form>
			            </div>
		            </div>

			    </div>
		    </div>
        </header>

        <div id="wrap">
            <aside id="gnb">

            </aside>
            <div id="contents">
                <?php
                   if($searchResults != null) { ?>
                    <section id="video_result">
                        <header>
                            <h2>동영상 검색 결과</h2>
					    </header>
                        <div class="container">
                            <ul class="video_list">
                            <?php 
							$vcount = 0;
                            foreach($searchResults as $v2) { ?>
                                <li>
								<?php
								echo $v2->imgPath;
								$name = explode("/html/",$v2->imgPath);
								$img = $name[1];
								$name = explode("/html/",$v2->videoPath);
                                $video = $name[1];?>
                                    <div class="thumbnail">
                                            <video id="myVideo<?php echo $vcount;?>" height="240px"  width="235px"  poster="<?php echo $img;?>"  controls >
                                                <source src="<?php echo $video;?>" type="video/mp4" />
                                                Your browser does not support HTML5 video.
                                            </video>    
                                    </div>
									<div class="video_quality"> </div>
                                    <div class="play_wrap" onclick="playFullScreen('myVideo<?php echo $vcount; ?>');" ></div>
                                </li>
                            <?php     ++$vcount; } ?>
                            </ul>
					    </div>
                    </section>
                    <?php } ?>
                <div ><img id="anigif" src="../../contents/img/animated.gif" style="position:absolute; display: none; z-index: 10; left:350px; top:400px;" alt="" /></div>
			</div>
        </div>
        <script type="text/javascript" src="contents/js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="contents/js/masonry.pkgd.min.js"></script>
        <script type="text/javascript" src="contents/js/default.js"></script>
        <script type="text/javascript">

    function popup(mylink, windowname, titlename) {
        if (!window.focus) return true;
        var href;
        if (typeof (mylink) == 'string')
            href = mylink;
        else
            href = mylink.href;
        var w = window.open(href, windowname, "width=800,height=800,scrollbars=yes,directories=0,titlebar=0,toolbar=0,location=0,status=0,menubar=0,resizable=yes,fullscreen=yes");
        w.document.title = titlename;
        w.focus();
        return false;
    }
        </script>
        <script type="text/javascript">
            function browseVideo() {
                var mobile = (/iphone|ipad|ipod|android/i.test(navigator.userAgent.toLowerCase()));
 
                if (mobile) {
                    navigator.userAgent.toLowerCase();
                    if (userAgent.search("android") > -1)
                        window.app.showImagePicker();
                }
                document.getElementById('<%= fUpload.ClientID %>').click();
            }
<%--            function browseVideo() {
                <% if(Request.UserAgent.Contains("Android")) { %>
                window.app.showVideoPicker();
                <% } %>
                document.getElementById('<%= fUpload.ClientID %>').click();
            }--%>
            function play1(videoname) {
                var mobile = (/iphone|ipad|ipod|android/i.test(navigator.userAgent.toLowerCase()));
                if (mobile) {
                    navigator.userAgent.toLowerCase();
                    if (userAgent.search("android") > -1)
                        window.app.play1(videoname);
                }
            }
            function playFullScreen(elementid) {
                element = document.getElementById(elementid);
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
                element.load();
                element.play();
            }
        </script>
        <script type="text/javascript">
            function DisableControl(controlId) {
                document.getElementById(controlId).style.visibility = 'hidden';
            }
            function DisableControl_SetTimeout(controlid, interval) {
                setTimeout("DisableControl('" + controlid + "')", interval);
            }
            function UploadFileNow() {
                ag = document.getElementById('anigif');
                ag.style.left = (window.innerWidth / 2 - 50) + 'px';
                ag.style.top = (window.innerHeight / 2 - 50) + 'px';
                $("#anigif").show();
                ag.style.visibility = 'visible';
                DisableControl_SetTimeout('anigif', 20000);
                var value = $("#fUpload").val();
                if (value != '') {
                    $("#form1").submit();
                }
            }
        </script>
    </form>
</body>
</html>

