<?php
class Index{
	protected $db;
	public function __construct($db){$this->db = $db;}
    
	public function home(){
        
        $returnHTML = $this->headerHTML();

        $bodyPages = array(1=>array(), 2=>array(), 3=>array(), 4=>array(), 5=>array(), 6=>array(), 7=>array(), 8=>array(), 9=>array(), 10=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, name, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($bodyPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $bodyPages[$oneRow->pages_id] = array(trim(stripslashes($oneRow->name)), trim(stripslashes($oneRow->short_description)));
            }
        }
        
        $returnHTML .= '<!-- Bnner Section -->
        <section class="banner-area">
            <div class="banner-area-bg"></div>
            <div class="shape-one"></div>
            <div class="banner-content-outside-box">
                <div class="banner-content-box">
                    <div class="inner">';
                        $bannObj = $this->db->getObj("SELECT name, description FROM banners WHERE banners_publish = 1 ORDER BY RAND() LIMIT 0,1", array());
                        if($bannObj){
                            while($oneRow = $bannObj->fetch(PDO::FETCH_OBJ)){
                                $name = stripslashes($oneRow->name);
                                $description = nl2br(stripslashes(trim((string) $oneRow->description)));
                                
                                $returnHTML .= "<h1 style=\"max-width:420px;\">$name</h1>
                                <div style=\"max-width:420px\" class=\"text\">$description</div>";
                            }
                        }

                        $video = '';
                        $tableObj = $this->db->getObj("SELECT youtube_url FROM videos WHERE videos_publish=1 ORDER BY videos_id ASC LIMIT 0,1", array());
                        if($tableObj){
                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                $video = trim(stripslashes($oneRow->youtube_url));
                            }
                        }
                        
                        $returnHTML .= '
                        <div class="link-box">
                            <a href="/appointments.html" class="main-btn main-btn-one"><span>Schedule an Appointments</span></a>
                        </div>
                    </div>
                    <div class="banner-image-wrapper banner-right">
                        <div class="image">
                            <div class="bg"></div>
                            <img src="/website_assets/images/person1.png" alt="">
                        </div>
                        <div class="video-box">
                            <a target="_blank" href="'.$video.'" class="overlay-link">
                                <i class="fa-sharp fa-solid fa-play icon-position"></i>
                            </a>
                            <h5>Intro Video</h5>
                        </div>
                        <div class="contact-number">
                            <img src="/website_assets/images/winner-card.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Bnner Section -->';
        
        $returnHTML .= '<!-- About Section -->        
        <section class="about-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="banner-image-wrapper">
                            <div class="image"><img src="/website_assets/images/paster1.png" alt=""></div>
                            <div class="years-of-experience">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="349px" height="359px">
                                    <defs>
                                        <filter filterUnits="userSpaceOnUse" id="Filter_0" x="0px" y="0px" width="349px" height="359px">
                                            <feOffset in="SourceAlpha" dx="-28.284" dy="28.284"></feOffset>
                                            <feGaussianBlur result="blurOut" stdDeviation="7.746"></feGaussianBlur>
                                            <feFlood flood-color="rgb(37, 59, 112)" result="floodOut"></feFlood>
                                            <feComposite operator="atop" in="floodOut" in2="blurOut"></feComposite>
                                            <feComponentTransfer>
                                                <feFuncA type="linear" slope="0.03"></feFuncA>
                                            </feComponentTransfer>
                                            <feMerge>
                                                <feMergeNode></feMergeNode>
                                                <feMergeNode in="SourceGraphic"></feMergeNode>
                                            </feMerge>
                                        </filter>
                                    </defs>
                                    <g filter="url(#Filter_0)">
                                        <path fill-rule="evenodd" fill="rgb(255, 255, 255)" d="M287.000,30.999 L117.000,30.999 C100.431,30.999 87.000,44.429 87.000,60.998 L87.000,270.999 L287.000,240.999 C303.464,238.598 317.000,227.567 317.000,210.999 L317.000,60.998 C317.000,44.429 303.569,30.999 287.000,30.999 Z">
                                        </path>
                                    </g>
                                </svg>
                                <h4>20 <span class="text">Years of Experiences</span></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="about-content">
                            <div class="section-title mb-20">
                                <h2>'.$bodyPages[3][0].'</h2>
                            </div>
                            <div class="text">
                                '.$bodyPages[3][1].'
                            </div>
                            <ul class="list">';
                                $metaUrl = $this->db->seoInfo('metaUrl');
                                foreach($metaUrl as $oneMetaUrl=>$label){
                                    $returnHTML .= "<li><a title=\"$label\" href=\"/$oneMetaUrl\">$label</a></li>";
                                }                    
                                $returnHTML .= '
                            </ul>
                            <a href="/services-main.html" class="main-btn main-btn-one"><span> Find Services</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>';
        
        $returnHTML .= '<!-- services Section -->
        <section class="service-area" style="padding-bottom:120px !important;">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-sub-title">Services</div>
                    <h2>Common Pest Control</h2>
                </div>
                <div class="service-wrapper">
                    <div class="row">';

                        $tableObj = $this->db->getObj("SELECT name, font_awesome, uri_value, short_description FROM services WHERE services_publish =1 ORDER BY RAND() LIMIT 0, 4", array());
                        if($tableObj){
                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                $name = trim(stripslashes($oneRow->name));
                                $font_awesome = trim(stripslashes($oneRow->font_awesome));
                                $uri_value = trim(stripslashes($oneRow->uri_value));
                                $short_description = trim(stripslashes($oneRow->short_description));

                                $returnHTML .= '<div class="col-lg-3 col-md-6" style="margin-bottom:30px;">
                                    <div class="service-box">
                                        <div class="icon">                                            
                                            <div class="svgIcon '.$font_awesome.'"></div>                                            
                                        </div>
                                        <h4><a href="/services/'.$uri_value.'.html">'.$name.'</a></h4>
                                        <div class="text">'.$short_description.'</div>
                                        <div class="link-btn"><a href="/services/'.$uri_value.'.html" class="main-btn main-btn-one main-btn-two"><span>Learn More</span></a></div>
                                    </div>
                                </div>';
                            }
                        }

                    $returnHTML .= '
                    </div>
                    <div class="text-center mt-30 mb-30"><a href="/services-main.html" class="main-btn main-btn-one"><span>More Services</span></a></div>
                </div>
            </div>
        </section>';

        $returnHTML .= '<!-- video Section -->
        <section class="video-area" style="margin-top:170px !important; position:relative !important;">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-sub-title">Videos</div>
                    <h2>Our Service Videos</h2>
                </div>
                <div class="video-wrapper">
                    <div class="row">';

                    $returnHTML .= '<!-- Video Section -->        
                    <section class="col-md-12 video-section">
                        <div class="container">
                            <div class="row">';

                            $video = '';
                            $tableObj = $this->db->getObj("SELECT videos_id, name, youtube_url FROM videos WHERE videos_publish=1 ORDER BY videos_id ASC LIMIT 0,3", array());
                            if($tableObj){
                                while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                    $id = trim($oneRow->videos_id);
                                    $video = trim(stripslashes($oneRow->youtube_url));
                                    $name = trim(stripslashes($oneRow->name));
                                    
                                    $videoImgUrl = ''; 
                                    $filePath = "./assets/accounts/video_$id".'_';
                                    $pics = glob($filePath."*.jpg");
                                    if(!$pics){
                                        $pics = glob($filePath."*.png");
                                    }
                                    if($pics){
                                        foreach($pics as $onePicture){
                                            $videoImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                        }
                                    }
                                    

                                    $returnHTML .= '
                                        <div class="col-md-4" style="border:0px solid red; margin:0 auto;">
                                            <div class="video-box" style="background-image: url('.$videoImgUrl.'); background-repeat: no-repeat; background-position: center; background-size: cover;">
                                                <div class="video-btn">
                                                    <a target="_blank" href="'.$video.'" class="show-effect"><span class="fa-sharp fa-solid fa-play"></span></a>
                                                </div>
                                            </div>
                                            <h4 class="category text-center" style="min-width:100%; padding:10px;">'.$name.'</h4>
                                        </div>'; 
                                }
                            }                      
                    
                    $returnHTML .= '</div>
                        </div>
                        <div class="text-center mt-30 mb-30"><a href="/videos-main.html" class="main-btn main-btn-one"><span>More Videos</span></a></div>
                    </section>';  

                    $returnHTML .= '
                    </div>
                </div>
            </div>
        </section>';
    
        $returnHTML .= '<!-- Whychoose us section -->
        <section class="why-choose-us-area" style="background-image: -webkit-linear-gradient( 90deg, rgba(233, 247, 242, 0.4) 0%, rgb(233, 247, 242) 100% );">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="section-title mb-20">
                            <h2>WHY CHOOSE US</h2>
                        </div>';

                        $tableObj = $this->db->getObj("SELECT name, description FROM why_choose_us WHERE why_choose_us_publish =1 ORDER BY RAND() LIMIT 0, 4", array());
                        if($tableObj){
                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                $name = trim(stripslashes($oneRow->name));
                                $description = nl2br(stripslashes(trim((string) $oneRow->description)));

                                $returnHTML .= '
                                <div class="choose-box">
                                    <div class="icon icon1">
                                        <i class="fa-solid fa-reply"></i>
                                    </div>
                                    <div class="choose-box-content">
                                        <h4>'.$name.'</h4>
                                        <div class="text">'.$description.'</div>
                                    </div>
                                </div>';
                            }
                        }

                        $returnHTML .= '
                    </div>
                    <div class="col-lg-5">
                        <div class="image-wrapper">
                            <div class="image">
                                <img src="/website_assets/images/image-3.jpg" alt="">
                            </div>
                            <a href="tel:'.$bodyPages[1][1].'" class="phone">
                                <img class="choose-img1" src="/website_assets/images/icon-3.png" alt="">
                                <div class="hover-image"><img src="/website_assets/images/icon-3.png" alt=""></div>
                            </a>
                            <a class="email" href="mailto:'.$bodyPages[2][1].'">
                                <img class="choose-img2" src="/website_assets/images/icon-4.png" alt="">
                                <div class="hover-image"><img src="/website_assets/images/icon-4.png" alt=""></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>';        
    
        $returnHTML .= '<!-- Blog Section -->
        <section class="blog-area">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-sub-title">News & Article</div>
                    <h2>Stay Update with Pesterminate</h2>
                </div>
                <div class="row">';

                    $tableObj = $this->db->getObj("SELECT news_articles_id, name, news_articles_date, created_by, uri_value, short_description FROM news_articles WHERE news_articles_publish =1 ORDER BY RAND() LIMIT 0, 3", array());
                    if($tableObj){
                        while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                            $news_articles_id = $oneRow->news_articles_id;
                            $name = trim(stripslashes($oneRow->name));
                            $created_by = trim(stripslashes($oneRow->created_by));
                            $news_articles_date = date('m/d/Y', strtotime($oneRow->news_articles_date));
                            $uri_value = trim(stripslashes($oneRow->uri_value));
                            $short_description = trim(stripslashes($oneRow->short_description));
                            $filePath = "./assets/accounts/news_$news_articles_id".'_';
                            $catPics = glob($filePath."*.jpg");
                            if(!$catPics){
                                $catPics = glob($filePath."*.png");
                            }

                            $catImgSrc = '/website_assets/images/missing-picture.jpg';                                            
                            if($catPics){
                                foreach($catPics as $onePicture){
                                    $catImgSrc = "//".OUR_DOMAINNAME.str_replace("./", '/', $onePicture);
                                }
                            }

                            $returnHTML .= '
                            <div class="col-lg-4 col-md-4">
                                <div class="blog-box">
                                    <div class="image"><a href="'.$uri_value.'.html"><img src="'.$catImgSrc.'" alt="" style="min-width:100%; max-height:100%;"></a></div>
                                    <h5 class="category" style="min-width:100%;">'.$name.'</h5>
                                    <!--p>'.$short_description.'</p-->
                                    <p class="post-meta">
                                        By <span> '.$created_by.'</span> - '.$news_articles_date.'
                                    </p>
                                    <a href="'.$uri_value.'.html" class="main-btn main-btn-one main-btn-two"><span> Learn More</span></a>
                                </div>
                            </div>';
                        }
                    }

                $returnHTML .= '
                </div>
            </div>
        </section>';

        $returnHTML .= '
        <section class="review-area" style="background-image: -webkit-linear-gradient( 90deg, rgba(233, 247, 242, 0.4) 0%, rgb(233, 247, 242) 100% );">
            <div class="container">
                <div class="section-title text-center">
                    <div class="section-sub-title">Testimonials</div>
                    <a href="/customer-reviews.html" title="Our Customer Reviews"><h2>Our Customer Reviews</h2></a>
                </div>
                <div class="row">
                    <div class="col-12 testinomalSection">                            
                        <div class="testinomalContainer" style="padding: 0 0px;">
                            <span id="nextArrow" class="arrow nextArrow"> <i class="fa fa-chevron-right"></i> </span>
                            <span id="previousArrow" class="arrow previousArrow"> <i class="fa fa-chevron-left"></i> </span>';
                            $bulletHTML = $ratingHTML = '';
                            $tableObj = $this->db->getObj("SELECT * FROM customer_reviews WHERE customer_reviews_publish = 1 ORDER BY RAND() ASC LIMIT 0,15", array());
                            if($tableObj){
                                $l=0;
                                $ratingData = array('5'=>'<li class="full"></li><li class="full"></li><li class="full"></li><li class="full"></li><li class="full"></li>', 
                                '4.5'=>'<li class="full"></li><li class="full"></li><li class="full"></li><li class="full"></li><li class="half"></li>', 
                                '4'=>'<li class="full"></li><li class="full"></li><li class="full"></li><li class="full"></li><li class="zero"></li>');
                                while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                    $l++;
                                    $active = $active1 = '';
                                    if($l==1){
                                        $active = ' active';
                                        $active1 = ' class="active"';
                                    }
                                    $bulletHTML .= "<li class=\"dot$active\"></li>";

                                    $name = trim(stripslashes($oneRow->name));
                                    $reviews_date = date('jS F Y', strtotime($oneRow->reviews_date));
                                    $reviews_rating = (string)$oneRow->reviews_rating;
                                    if(!array_key_exists($reviews_rating, $ratingData)){$reviews_rating = '5';}
                                    $reviewsRating = $ratingData[$reviews_rating];
                                    $description = nl2br(trim(stripslashes($oneRow->description)));

                                    $ratingHTML .= '<div'.$active1.'>
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-12 col-md-3">
                                                <h5>'.$name.'</h5>
                                                <h6>'.$reviews_date.'</h6>
                                                <ul class="rating">
                                                    '.$reviewsRating.'
                                                </ul>
                                            </div>
                                            <div class="col-lg-9 col-sm-12 col-md-9 contents">
                                                <p>'.$description.'</p>
                                            </div>                                        
                                        </div>
                                    </div>';
                                    
                                }
                            }
                            $returnHTML .= '<ul class="dots" id="dots">
                                '.$bulletHTML.'
                            </ul>
                            <div class="reviewContainer" id="reviewContainer">
                                '.$ratingHTML.'
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial Section -->
        </section>
        ';        
        
        $returnHTML .= '<!-- Newsletter -->
        <section class="news-area">
            <div class="section-bg"></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <h2>Sign Up To <span>Our Newsletter</span> To Get The Latest Offers.</h2>
                    </div>
                    <div class="col-lg-6">
                        <div class="news-form">
                            <form class="ajax-sub-form" method="post">
                                <div class="form-group">
                                    <input type="email" placeholder="Enter Your Email Address..." id="subscription-email">
                                    <button type="submit" class="main-btn main-btn-one"><span>Subscribe</span></button>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        ';
        
        
        
        $returnHTML .= $this->footerHTML();

		return $returnHTML;
	}

    public function appointments(){
        $returnHTML = $this->headerHTML();

        $returnHTML .= '
        <!-- Search Sevices -->
                
        <section class="contact-form-section">
            <div class="container">
                <div class="sec-title text-center">
                    <h2>Do an <span>Appointment</span> To Us</h2>
                    <h3 style="color:#d00000">or <span>Get a</span> Free Quote</h3>
                </div>
                <!--Contact Form-->
                <div class="contact-form">
                    <form action="#" id="frmAppointments" onsubmit="return saveAppointments(event);" style="border:0px solid red; margin:0 auto;">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="dropdown2 bootstrap-select2 dropup2">
                                    <select name="services_id" required>
                                        <option value="">Select Service Type</option>';
                                        
                                        $tableObj = $this->db->getObj("SELECT services_id, name FROM services WHERE services_publish = 1 ORDER BY name ASC", array());
                                        if($tableObj){
                                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                                $name = trim(stripslashes($oneRow->name));
                                                $returnHTML .= "<option value=\"$oneRow->services_id\">$name</option>";
                                            }
                                        }
                                        
                                        $returnHTML .= '
                                    </select>
                                </div>                            
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="email" placeholder="Your Email" required>
                            </div>
                            <div class="form-group col-md-12">
                                <input type="text" name="name" placeholder="Your Name" required>
                            </div>
                            
                            <div class="form-group col-md-6">
                                <input type="text" name="address" placeholder="Address" required>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="phone" placeholder="Phone" required>
                            </div>

                            <div class="form-group col-md-12">
                                <textarea name="description" placeholder="Description" required></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <div id="mathCaptcha"></div>
                                <span id="errRecaptcha" style="color:red"></span>
                            </div>

                            <div class="form-group col-md-12">
                                <div class="text-center">
                                    <input id="form_botcheck" name="form_botcheck" class="form-control" type="hidden" value="">
                                    <button type="submit" id="submitAppointments" class="theme-btn btn-style-one" data-loading-text="Please wait..."><span>Get Started</span></button>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <span id="msgAppointments"></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <script src="/assets/js/mathCaptcha.js"></script>
        ';

        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }

    function sendAppointments(){
        //================Email Options Here==============//
        
        $returnData = array();
        $returnData['savemsg'] = 'error';
        $services_id = intval($_POST['services_id']??0);
		$name = addslashes(trim($_POST['name']??''));
		$phone = addslashes(trim($_POST['phone']??''));
		$email = addslashes(trim($_POST['email']??''));	
		$description = addslashes(trim($_POST['description']??''));	
		$address = addslashes(trim($_POST['address']??''));	
        
        $customerData = array();
		$customerData['name'] = $name;
		$customerData['phone'] = $phone;
		$customerData['email'] = $email;
		$customerData['address'] = $address;
		
        $customers_id = 0;
        $queryManuObj = $this->db->getObj("SELECT customers_id FROM customers WHERE name = :name AND phone = :phone", array('name'=>$name, 'phone'=>$phone));
        if($queryManuObj){
            $customers_id = $queryManuObj->fetch(PDO::FETCH_OBJ)->customers_id;						
        }        
        if($customers_id==0){
            $customerData['offers_email'] = 1;
            $customerData['customers_publish'] = 1;
            $customerData['users_id'] = 0;
            $customerData['last_updated'] = date('Y-m-d H:i:s');
            $customerData['created_on'] = date('Y-m-d H:i:s');
            // var_dump($customerData);exit;
            $customers_id = $this->db->insert('customers', $customerData);
        }
        if($customers_id>0 && $services_id>0){
            
            $appointmentsData = array();
            $appointmentsData['created_on'] = date('Y-m-d H:i:s');
            $appointmentsData['last_updated'] = date('Y-m-d H:i:s');
            $appointmentsData['users_id'] = 1;
            $appointmentsData['appointments_publish'] = 1;
            $appointmentsData['notifications'] = 1;
            $appointments_no = 1;
            $queryObj = $this->db->getObj("SELECT appointments_no FROM appointments ORDER BY appointments_no DESC LIMIT 0, 1", array());
            if($queryObj){
                $appointments_no = intval($queryObj->fetch(PDO::FETCH_OBJ)->appointments_no)+1;
            }

            $appointmentsData['appointments_no'] = $appointments_no;
            $appointmentsData['services_id'] = $services_id;
            $appointmentsData['customers_id'] = $customers_id;
            $appointmentsData['services_type'] = '';
            $appointmentsData['description'] = $description;
            $appointmentsData['appointments_date'] = date('Y-m-d');
            // var_dump($appointmentsData);exit;
            $appointments_id = $this->db->insert('appointments', $appointmentsData);
            if($appointments_id){

                //################Appointment Email#################
                $returnStr = '';
                $email = array_key_exists('email', $_POST)?$_POST['email']:'';
                
                if($email =='' || is_null($email)){
                    $returnStr = 'Could not send mail because of missing your email address.';
                }
                else{
                    
                    $fromName = trim(stripslashes((string) $_POST['name']??''));
                    $phone = nl2br(trim(stripslashes((string) $_POST['phone']??'')));
                    $note = nl2br(trim(stripslashes((string) $_POST['description']??'')));            
                    $address = nl2br(trim(stripslashes((string) $_POST['address']??'')));            
                    $subject = '[New message] From '.LIVE_DOMAIN." Contact Form";
                                
                    
                    $message = "<html>";
                    $message .= "<head>";
                    $message .= "<title>$subject</title>";
                    $message .= "</head>";
                    $message .= "<body>";
                    $message .= "<p>";
                    $message .= "Dear <i><strong>$fromName</strong></i>,<br />";
                    $message .= "We received your request for contact.<br /><br />";
                    $message .= "You wrote:<br />";
                    $message .= "Phone: $phone<br>";
                    $message .= "Email: $email<br>";
                    $message .= "Address: $address<br>";
                    $message .= "Message: $note";
                    $message .= "</p>";
                    $message .= "<p>";
                    $message .= "<br />";
                    $message .= "Thank you for contacting us.";
                    $message .= "<br />";
                    $message .= "We will reply as soon as possible.";
                    $message .= "</p>";
                    $message .= "</body>";
                    $message .= "</html>";            

                    $do_not_reply = $this->db->supportEmail('do_not_reply');
                    $headers = array();
                
                    $info = $this->db->supportEmail('info');
                    $headers[] = "From: ".COMPANYNAME;
                    $headers[] = "Reply-To: ".$do_not_reply;
                    $headers[] = "Organization: ".COMPANYNAME;
                    $headers[] = "MIME-Version: 1.0";
                    $headers[] = "Content-type: text/html; charset=iso-8859-1";
                    $headers[] = "X-Priority: 3";
                    $headers[] = "X-Mailer: PHP".phpversion();

                    
                    if(mail($email, $subject, $message, implode("\r\n", $headers))){
                        $returnStr = 'sent';
                        
                        $info = $this->db->supportEmail('info');
                        $headersAdmin = array();
                        $headersAdmin[] = "From: ".$email;
                        $headersAdmin[] = "Reply-To: ".$do_not_reply;
                        $headersAdmin[] = "Organization: ".COMPANYNAME;
                        $headersAdmin[] = "MIME-Version: 1.0";
                        $headersAdmin[] = "Content-type: text/html; charset=iso-8859-1";
                        $headersAdmin[] = "X-Priority: 3";
                        $headersAdmin[] = "X-Mailer: PHP".phpversion();                
                        
                        $message = "<html>";
                        $message .= "<head>";
                        $message .= "<title>$subject</title>";
                        $message .= "</head>";
                        $message .= "<body>";
                        $message .= "<p>";
                        $message .= "Dear Admin of <i><strong>".COMPANYNAME."</strong></i>,<br />";
                        $message .= "We received a Contact request from $fromName.<br /><br />";
                        $message .= "He / She wrotes:<br />";
                        $message .= "Phone: $phone<br>";
                        $message .= "Email: $email<br>";
                        $message .= "Address: $address<br>";
                        $message .= "Message: $note";
                        $message .= "</p>";
                        $message .= "<p>";
                        $message .= "<br />";
                        $message .= "Please reply him/her as soon as possible.";
                        $message .= "</p>";
                        $message .= "</body>";
                        $message .= "</html>";
                        
                        mail($info, $subject, $message, implode("\r\n", $headersAdmin));
                        
                    }
                    else{
                        $returnStr = "Sorry! Could not send mail. Try again later.";
                    }
                }
                // return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
                //################Appointment Email End#################
		        $returnData['savemsg'] = 'Sent';
            }
        }
        
        return json_encode($returnData);
    }

	public function whyChooseUs(){  
        $returnHTML = $this->headerHTML();
        $returnHTML .= '
        <section class="why-choose-us-area" style="padding:20px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-md-10" style="margin:0 auto;">';

                        $tableObj = $this->db->getObj("SELECT name, description FROM why_choose_us WHERE why_choose_us_publish =1 ORDER BY RAND() LIMIT 0, 4", array());
                        if($tableObj){
                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                $name = trim(stripslashes($oneRow->name));
                                $description = nl2br(stripslashes(trim((string) $oneRow->description)));

                                $returnHTML .= '
                                <div class="choose-box">
                                    <div class="icon icon1">
                                        <i class="fa-solid fa-reply"></i>
                                    </div>
                                    <div class="choose-box-content">
                                        <h4>'.$name.'</h4>
                                        <div class="text">'.$description.'</div>
                                    </div>
                                </div>';
                            }
                        }

                    $returnHTML .= '</div>                       
                </div>
            </div>
        </section>';
        $returnHTML .= $this->footerHTML();
		return $returnHTML;
	}

    public function newsMain(){ 
        
        $returnHTML = $this->headerHTML();

        $returnHTML .= '
        <section class="blog-area" style="padding:20px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-md-10" style="margin:0 auto;">';
                        $pr = 0;
                        $tableObj = $this->db->getObj("SELECT news_articles_id, name, news_articles_date, created_by, uri_value, short_description FROM news_articles WHERE news_articles_publish =1 ORDER BY RAND() LIMIT 0, 3", array());
                        if($tableObj){
                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                $news_articles_id = $oneRow->news_articles_id;
                                $name = trim(stripslashes($oneRow->name));
                                $created_by = trim(stripslashes($oneRow->created_by));
                                $news_articles_date = date('m/d/Y', strtotime($oneRow->news_articles_date));
                                $uri_value = trim(stripslashes($oneRow->uri_value));
                                $short_description = trim(stripslashes($oneRow->short_description));
                                $filePath = "./assets/accounts/news_$news_articles_id".'_';
                                $catPics = glob($filePath."*.jpg");
                                if(!$catPics){
                                    $catPics = glob($filePath."*.png");
                                }

                                $catImgSrc = '/website_assets/images/missing-picture.jpg';                                            
                                if($catPics){
                                    foreach($catPics as $onePicture){
                                        $catImgSrc = "//".OUR_DOMAINNAME.str_replace("./", '/', $onePicture);
                                    }
                                }

                                $returnHTML .= "
                                <div class=\"single-card card-style-one mt-30 mb-30\">";
                                
                                if($pr%2 == 0){
                                    
                                    $returnHTML .= "
                                    <div class=\"row ml-0 mr-0\">
                                        <div class=\"col-md-4 order-md-1 text-right\" style=\"border:0px solid red;\">                                            
                                            <a href=\"$uri_value.html\"><img src=\"$catImgSrc\" alt=\"\"></a>
                                        </div>
                                        <div class=\"col-md-8 order-md-2\" style=\"border:0px solid red;\">
                                            <h2 class=\"featurette-heading-new lh-1\">$name. </h2>
                                            <p class=\"lead\">$short_description.</p>
                                            <br>
                                            <p class=\"post-meta\">
                                                By <span> $created_by</span> - $news_articles_date
                                            </p>
                                            <div class=\"arrow d-flex justify-content-left\">
                                                <a class=\"btn btn-warning\" href=\"$uri_value.html\">Read More<a>
                                            </div>
                                        </div>                                        
                                    </div>
                                    ";
                                } else {
                                    $returnHTML .="
                                    <div class=\"row ml-0 mr-0\" >
                                        <div class=\"col-md-8\">
                                            <h2 class=\"featurette-heading-new lh-1 float-right\">$name. </h2><br><br>
                                            <p class=\"lead float-right\">$short_description.</p><br>
                                            <br>
                                            <p class=\"post-meta\">
                                                By <span> $created_by</span> - $news_articles_date
                                            </p>
                                            <div class=\"arrow d-flex float-right\">
                                                <a class=\"btn btn-warning  float-right\" href=\"$uri_value.html\">Read More<a>
                                            </div>
                                        </div>
                                        <div class=\"col-md-4\">                                            
                                        <a href=\"$uri_value.html\"><img src=\"$catImgSrc\" alt=\"\"></a>
                                        </div>
                                    </div>";       
                                }
                                $returnHTML .= "</div>";
                            }
                        }
                        $returnHTML .= '                    
                    </div>
                </div>
            </div>
        </section>';
        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }

    public function fetchNews(){

        $returnStr = '';

        if(isset($_POST['start'])){
            $pr = 0;
            $start = $_POST['start'];
            $tablePageObj = $this->db->getObj("SELECT * FROM news_articles WHERE news_articles_publish = 1 limit $start,1", array());
            // var_dump('test');exit;
            if($tablePageObj){
                // var_dump($tablePageObj);exit;
                while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){
                    $pr++;
                    $news_articles_id = $onePageRow->news_articles_id;
                    $short_description = nl2br(trim(stripslashes($onePageRow->short_description)));
                    $uri_value = nl2br(trim(stripslashes($onePageRow->uri_value)));
                    $description = nl2br(trim(stripslashes($onePageRow->description)));
                    $name = nl2br(trim(stripslashes($onePageRow->name)));

                    $filePath = "./assets/accounts/news_$news_articles_id".'_';
                    $catPics = glob($filePath."*.jpg");
                    if(!$catPics){
                        $catPics = glob($filePath."*.png");
                    }

                    $catImgSrc = '/website_assets/images/missing-picture.jpg';                                            
                    if($catPics){
                        foreach($catPics as $onePicture){
                            $catImgSrc = "//".OUR_DOMAINNAME.str_replace("./", '/', $onePicture);
                        }
                    }

                    $returnStr .= "
                                    <div class=\"row featurette\" >
                                        <div class=\"col-md-9 order-md-2\">
                                            <h2 class=\"featurette-heading-new lh-1\">$name</h2>
                                            <p class=\"lead\">$short_description</p>
                                            <br>
                                            <div class=\"arrow d-flex justify-content-left\">
                                                <a class=\"btn btn-warning\" href=\"$uri_value.html\">Read More<a>
                                                <a href=\"'.$uri_value.'.html\"></a>  
                                            </div>
                                        </div>
                                        <div class=\"col-md-3 order-md-1\">                                            
                                        <a href=\"blog-details.html\"><img src=\"$catImgSrc\" alt=\"\"></a>
                                        </div>
                                        <div class=\"separator\"></div>
                                    </div>
                                    <!--<div class=\"arrow d-flex justify-content-center\" style=\"border:0px solid red; margin:0 auto;\">
                                        <a class=\"btn btn-info\" id=\"loadMoreBtn\" href=\"#\" onclick=\"call_lazy_load(event)\">Load More<a> 
                                    </div>-->";


                }
            }
        }

        // return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
        echo $returnStr;
  
        

        // if(isset($_POST['start'])){
        //     // $start = mysqli_real_escape_string($con, $_POST['start']);
        //     //echo $start;exit;
        //     $sql = "select * from load_more limit $start,7";
        //     $res = mysqli_query($con, $sql);
        //     if(mysqli_num_rows($res) > 0){
        //         $html = "";
        //         while($row = mysqli_fetch_assoc($res)){		
        //             $html .= "<h2>".$row['heading']."</h2>";
        //         }
        //         echo $html;

        //     }
        // } else {
        //     $returnStr = 'Could not send mail because of missing your email address.';
        // }	
	
		

    }

    public function newsMainOLD(){ 
        
        $returnHTML = $this->headerHTML();

        $returnHTML .= '
        <br><br>
        <section>
            <div class="container">                    
                <div class="row">
                    ';

                        $tableObj = $this->db->getObj("SELECT news_articles_id, name, news_articles_date, created_by, uri_value, short_description FROM news_articles WHERE news_articles_publish =1 ORDER BY RAND() LIMIT 0, 3", array());
                        // var_dump($tableObj);exit;
                        if($tableObj){
                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                $news_articles_id = $oneRow->news_articles_id;
                                $name = trim(stripslashes($oneRow->name));
                                $created_by = trim(stripslashes($oneRow->created_by));
                                $news_articles_date = date('m/d/Y', strtotime($oneRow->news_articles_date));
                                $uri_value = trim(stripslashes($oneRow->uri_value));
                                $short_description = trim(stripslashes($oneRow->short_description));
                                $filePath = "./assets/accounts/news_$news_articles_id".'_';
                                $catPics = glob($filePath."*.jpg");
                                if(!$catPics){
                                    $catPics = glob($filePath."*.png");
                                }

                                $catImgSrc = '/website_assets/images/missing-picture.jpg';                                            
                                if($catPics){
                                    foreach($catPics as $onePicture){
                                        $catImgSrc = "//".OUR_DOMAINNAME.str_replace("./", '/', $onePicture);
                                    }
                                }

                                $returnHTML .= '<div class="col-lg-4 news-block-one">
                                    <div class="inner-box wow fadeInDown" data-wow-duration="1500ms">
                                        <div class="image"><a href="blog-details.html"><img src="'.$catImgSrc.'" alt=""></a></div>
                                        <h5><strong>'.$name.'</strong></h5>
                                        <p><a href="'.$uri_value.'.html">'.$short_description.'</a></p>
                                        <div class="post-meta">By <a href="#"><span> '.$created_by.'</span></a> - <a href="#">'.$news_articles_date.'</a></div>
                                        <div class="link-btn"><a href="'.$uri_value.'.html" class="theme-btn btn-style-one style-two"><span> Learn More</span></a></div>
                                    </div>
                                </div>';
                            }
                        }
                        $returnHTML .= '
                    
                </div>';                    
                $returnHTML .= '
            </div>
        </section><br><br>';
        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }

    public function newses(){
        
        $id = $GLOBALS['id'];

        if($id>0){
            $returnHTML = $this->headerHTML();

            $returnHTML .= '
            <section>
                <div class="container">                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="single-card card-style-one mt-30 mb-30">';
                    
                            $tablePageObj = $this->db->getObj("SELECT * FROM news_articles WHERE news_articles_id = $id", array());
                            if($tablePageObj){
                            
                                while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){

                                    $serviceImgUrl = ''; 
                                    $filePath = "./assets/accounts/news_$id".'_';
                                    $pics = glob($filePath."*.jpg");
                                    if(!$pics){
                                        $pics = glob($filePath."*.png");
                                    }
                                    if($pics){
                                        foreach($pics as $onePicture){
                                            $serviceImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                        }
                                    }
                                    if(!empty($serviceImgUrl)){
                                        $serviceImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$serviceImgUrl\"  width=\"500\" height=\"500\" style=\"height:500px !important;\">";
                                    }
                                    else{
                                        $serviceImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\"  width=\"500\" height=\"500\" >";
                                    } 

                                    $returnHTML .= "<div class=\"card-content\">
                                        <div class=\"row\">
                                            <div class=\"col-md-7 order-md-2\">
                                                <p class=\"txtJustfy\">".nl2br(trim(stripslashes($onePageRow->short_description)))."</p>
                                                <p class=\"txtJustfy\">".nl2br(trim(stripslashes($onePageRow->description)))."</p>
                                                
                                                <button class=\"btn btn-warning\" onclick=\"history.back()\">Go Back</button>
                                                <br><br>
                                            </div>
                                            <div class=\"col-md-5 order-md-1\">
                                                $serviceImg
                                            </div>                                        
                                        </div>
                                    </div>";
                                }
                            }
                            $returnHTML .= '                            
                            </div>
                        </div>
                    </div>
                </div>
            </section>';
            $returnHTML .= $this->footerHTML();
        }
        else{
            $tableObj = $this->db->getObj("SELECT uri_value FROM pages WHERE pages_id = 1", array());
            $uri_value = $tableObj->uri_value;
            $returnHTML = "<meta http-equiv = \"refresh\" content = \"0; url = '/$uri_value.html'\" />";
        }
        
		return $returnHTML;
    }

    public function pages(){
        
        $id = $GLOBALS['id'];

        $bodyPages = array(1=>array(), 2=>array(), 3=>array(), 4=>array(), 5=>array(), 6=>array(), 7=>array(), 8=>array(), 9=>array(), 10=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, name, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($bodyPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $bodyPages[$oneRow->pages_id] = array(trim(stripslashes($oneRow->name)), trim(stripslashes($oneRow->short_description)));
            }
        }

        if($id>0){

            $returnHTML = $this->headerHTML();

            $returnHTML .= '
            <section>

                <!--<div class="container">                    
                    <div class="row">
                        <div class="col-md-12">-->

                <div class="container" style="min-width:80% !important; border:0px solid red;">                    
                    <div class="row">
                        <div class="col-md-12" style="border:0px solid red;">


                            <div class="single-card card-style-one pl-2 mt-4 mb-3" style="border:0px solid red; padding-top:0px !important;">';
                    
                            $tablePageObj = $this->db->getObj("SELECT * FROM pages WHERE pages_id = $id", array());
                            if($tablePageObj){
                            
                                while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){

                                    $page_id = $onePageRow->page_id;
                                    $name = trim(stripslashes($onePageRow->name));
                                    $page_create_date = date('m/d/Y', strtotime($onePageRow->created_on));
                                    $uri_value = trim(stripslashes($onePageRow->uri_value));

                                    $pageImgUrl = ''; 
                                    $filePath = "./assets/accounts/page_$id".'_';
                                    $pics = glob($filePath."*.jpg");
                                    if(!$pics){
                                        $pics = glob($filePath."*.png");
                                    }
                                    if($pics){
                                        foreach($pics as $onePicture){
                                            $pageImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                        }
                                    }
                                    if(!empty($pageImgUrl)){
                                        $pageImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$pageImgUrl\">";
                                    }
                                    else{
                                        $pageImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\" >";
                                    } 

                                
                                    $returnHTML .= "
                                    <div class=\"card-content\">
                                        <div class=\"row\">";                                            

                                            $returnHTML .= "<div class=\"col-md-3 order-md-1\">";
                                                
                                            $returnHTML .= $this->sidebarHTML();   

                                            $returnHTML .= "</div>  

                                            <div class=\"row col-md-9\" style=\"height:0px !important; border:0px solid red !important;\">
                                                
                                                <div class=\"col-md-12\">
                                                    <h2 class=\"mb-10 fontdescription_two\">$name</h2>
                                                </diV>

                                                <div class=\"row\" style=\"margin-top:20px;\">
                                                    <div class=\"banner-image-wrapper col-md-5\" style=\"padding-top:20px; padding-left:20px; padding-right:20px;\">
                                                        <p class=\"text-center\" style=\"border:0px solid red;\">$pageImg</p>
                                                    </diV>
                                                    <div class=\"about-area col-md-7\" style=\"padding-left:0px !important; padding-right:20px; padding-top:0px; padding-bottom:0px ;\"> 
                                                        <div class=\"about-content\">                                                            
                                                            <p style=\"font-family:Roboto !important; font-style: normal; font-size:20px; line-height:30px; font-weight: 300;\" class=\"txtJustfy\">".nl2br(trim(stripslashes($onePageRow->short_description)))."</p>
                                                        </div>                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";
                                }
                            }
                            $returnHTML .= '                            
                            </div>





                        </div>
                    </div>';                    
                    $returnHTML .= '
                </div>
            </section>';
            $returnHTML .= $this->footerHTML();
        }
        else{
            $tableObj = $this->db->getObj("SELECT uri_value FROM pages WHERE pages_id = 1", array());
            $uri_value = $tableObj->uri_value;
            $returnHTML = "<meta http-equiv = \"refresh\" content = \"0; url = '/$uri_value.html'\" />";
        }
        
		return $returnHTML;
    }


    public function aboutPesterminate(){

        
        
        $id = 3;

        $bodyPages = array(1=>array(), 2=>array(), 3=>array(), 4=>array(), 5=>array(), 6=>array(), 7=>array(), 8=>array(), 9=>array(), 10=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, name, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($bodyPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $bodyPages[$oneRow->pages_id] = array(trim(stripslashes($oneRow->name)), trim(stripslashes($oneRow->short_description)));
            }
        }
        
        // if($id>0){

            
            $returnHTML = $this->headerHTML();

            $returnHTML .= '
            <section>

                <!--<div class="container">                    
                    <div class="row">
                        <div class="col-md-12">-->

                <div class="container" style="min-width:80% !important; border:0px solid red;">                    
                    <div class="row">
                        <div class="col-md-12" style="border:0px solid red;">


                            <div class="single-card card-style-one pl-2 mt-4 mb-3" style="border:0px solid red; padding-top:0px !important;">';
                    
                            $tablePageObj = $this->db->getObj("SELECT * FROM pages WHERE pages_id = $id", array());
                            if($tablePageObj){
                            
                                while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){

                                    $page_id = $onePageRow->page_id;
                                    $name = trim(stripslashes($onePageRow->name));
                                    $page_create_date = date('m/d/Y', strtotime($onePageRow->created_on));
                                    $uri_value = trim(stripslashes($onePageRow->uri_value));

                                    $pageImgUrl = ''; 
                                    $filePath = "./assets/accounts/page_$id".'_';
                                    $pics = glob($filePath."*.jpg");
                                    if(!$pics){
                                        $pics = glob($filePath."*.png");
                                    }
                                    if($pics){
                                        foreach($pics as $onePicture){
                                            $pageImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                        }
                                    }
                                    if(!empty($pageImgUrl)){
                                        $pageImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$pageImgUrl\">";
                                    }
                                    else{
                                        $pageImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\" >";
                                    } 

                                
                                    $returnHTML .= "
                                    <div class=\"card-content\">
                                        <div class=\"row\">";                                            

                                            $returnHTML .= "<div class=\"col-md-3 order-md-1\">";
                                                
                                            $returnHTML .= $this->sidebarHTML();   

                                            $returnHTML .= "</div>  

                                            <div class=\"row col-md-9\" style=\"border:0px solid red !important;\">
                                                
                                                <div class=\"col-md-12\">
                                                    <h2 class=\"mb-10 fontdescription_two\">$name</h2>
                                                </diV>
                                                <!--div class=\"col-md-12\" style=\"margin-top:20px;\">
                                                    <p class=\"txtJustfy\">".nl2br(trim(stripslashes($onePageRow->short_description)))."</p>
                                                </diV-->

                                                <div class=\"row\" style=\"margin-top:20px;\">
                                                    <div class=\"banner-image-wrapper col-md-5\" style=\"padding-top:20px; padding-left:20px; padding-right:20px;\">
                                                        <p class=\"text-center\" style=\"border:0px solid red;\">$pageImg</p>
                                                    </diV>
                                                    <div class=\"about-area col-md-7\" style=\"padding-left:0px !important; padding-right:20px; padding-top:0px; padding-bottom:0px ;\"> 
                                                    
                                                        <div class=\"about-content\">
                                                            <!--div class=\"section-title mb-0\">
                                                                <h2>$bodyPages[3][0]</h2>
                                                            </div>
                                                            <div class=\"text\">
                                                                $bodyPages[3][1]
                                                            </div-->
                                                            <p style=\"font-family:Roboto !important; font-style: normal; font-size:20px; line-height:30px; font-weight: 300; font-display: swap;\" class=\"txtJustfy\">".nl2br(trim(stripslashes($onePageRow->short_description)))."</p>
                                                            <ul class=\"list\">";
                                                                $metaUrl = $this->db->seoInfo('metaUrl');
                                                                foreach($metaUrl as $oneMetaUrl=>$label){
                                                                    $returnHTML .= "<li><a style=\"font-family:Roboto !important; font-style: normal; font-size:20px; line-height:30px; font-weight: 300; font-display: swap;\" title=\"$label\" href=\"/$oneMetaUrl\">$label</a></li>";
                                                                }                    
                                                                $returnHTML .= "
                                                            </ul>                                                           
                                                        </div>                                                        
                                                    </div>
                                                    <div class=\"row\" style=\"margin:0 15px\">
                                                        <div class=\"col-lg-6 mb-20\">
															<div class=\"equalHeight single-card dslc-info-box-main eq_col\">
															    <div class=\"dslc-info-box-title\">
																	<h4 class=\"card-title\" style=\"padding-left:15px;font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;\">WHAT WE DO</h4>
																</div>							
															    <div class=\"card-content dslc-info-box-content\">
																	<p class=\"mb-0\" style=\"font-family:Roboto !important; font-style: normal; font-size:20px; line-height:30px; font-weight: 300; font-display: swap;\">Our team of skilled technicians is fully trained and equipped to handle all types of pest control issues. We use the latest techniques and products to ensure that your property is protected from pests without causing harm to your health or the environment. We are committed to using only the most effective and safe pest control methods to keep your home or business pest-free.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class=\"col-lg-6 mb-20\">
                                                            <div class=\"equalHeight single-card  dslc-info-box-main eq_col\">
                                                                <div class=\"dslc-info-box-title\">
                                                                    <h4 class=\"card-title\" style=\"padding-left:15px; font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;\">HOW WE DO</h4>
                                                                </div>							
                                                                <div class=\"card-content dslc-info-box-content\">
                                                                    <p class=\"mb-0\" style=\"font-family:Roboto !important; font-style: normal; font-size:20px; line-height:30px; font-weight: 300; font-display: swap;\">At our company, we believe that prevention is key when it comes to pest control. That's why we offer a wide range of services designed to help you identify and prevent pest infestations before they become a major problem. Whether you're dealing with rodents, insects, or other pests, we have the tools and expertise to help you take back control of your space.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";
                                }
                            }
                            $returnHTML .= '                            
                            </div>





                        </div>
                    </div>';                    
                    $returnHTML .= '
                </div>
            </section>';
            $returnHTML .= $this->footerHTML();
        // }
        // else{
        //     $tableObj = $this->db->getObj("SELECT uri_value FROM pages WHERE pages_id = 1", array());
        //     $uri_value = $tableObj->uri_value;
        //     $returnHTML = "<meta http-equiv = \"refresh\" content = \"0; url = '/$uri_value.html'\" />";
        // }
        
		return $returnHTML;
    }

    
    public function videosMain(){ 
        
        $returnHTML = $this->headerHTML();

        // $returnHTML .= '
        // <section>
        //     <div class="container">
        //         <div class="row">
        //             <div class="col-md-10" style="margin:0 auto;">';
        
        $returnHTML .='
        <section>
            <div class="container" style="min-width:80% !important; border:0px solid red;">                    
                <div class="row">
                    <div class="col-md-12" style="border:0px solid red;">

                        <div class="single-card card-style-one pl-2 mt-4 mb-3" style="border:0px solid red; padding-top:0px !important;">';

                                $returnHTML .= "
                                <div class=\"card-content\">
                                    <div class=\"row\">";                                            

                                        $returnHTML .= "<div class=\"col-md-3 order-md-1\">";
                                            
                                        $returnHTML .= $this->sidebarHTML();   

                                        $returnHTML .= "</div> "; 

                                        $returnHTML .= "<div class=\"row col-md-9\" style=\"border:0px solid red !important;\">"; 
                                        
                                                            $returnHTML .= '<!-- services Section -->
                                                            <section class="video-area" style="background-image: -webkit-linear-gradient( 90deg, rgba(233, 247, 242, 0.4) 0%, rgb(233, 247, 242) 100% ) !important; padding: 12px 0 12px; width:100%; border:0px solid red !important; position:relative !important;">
                                                                <div class="container">
                                                                    <!--div class="section-title text-center">                                                        
                                                                        <h5 style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;" class="mb-10 mt-50 fontdescription_two">Our Works Videos</h5>
                                                                    </div-->
                                                                    <div class="section-title text-center">                                            
                                                                        <h2>Our Works Videos</h2>
                                                                    </div>
                                                                    <div class="video-wrapper">
                                                                        
                                                                        <div class="row">                                            
                                                                            <section class="col-md-12 video-section">
                                                                                <div class="container">
                                                                                    <div class="row" style="border:0px solid red !important;">';

                                                                                    $pr = 0;
                                                                                    $tablePageObj = $this->db->getObj("SELECT * FROM videos WHERE videos_publish = 1", array());
                                                                                    // var_dump('test');exit;
                                                                                    if($tablePageObj){
                                                                                
                                                                                        while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){
                                                            
                                                                                            $pr++;
                                                                                            $videos_id = $onePageRow->videos_id;
                                                                                            $name = nl2br(trim(stripslashes($onePageRow->name)));
                                                                                            $video = trim(stripslashes($onePageRow->youtube_url));

                                                                                            $videoImgUrl = ''; 
                                                                                            $filePath = "./assets/accounts/video_$videos_id".'_';
                                                                                            $pics = glob($filePath."*.jpg");
                                                                                            if(!$pics){
                                                                                                $pics = glob($filePath."*.png");
                                                                                            }
                                                                                            if($pics){
                                                                                                foreach($pics as $onePicture){
                                                                                                    $videoImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                                                                                }
                                                                                            }
                                                        
                                                                                                        
                                                                                            // var_dump($services_uri);exit;                                                 
                                                                                                    
                                                        
                                                                                            $returnHTML .='
                                                                                                <!--div class="col-md-6" style="padding-left:20px;padding-right:20px; border:0px solid red !important;">
                                                                                                <div class="pp-content-post pp-content-grid-post pp-grid-default"-->
                                                                                                    
                                                                                                                                                                
                                                                                                <div class="col-md-4 text-center" style="border:0px solid red; margin:0 auto;">
                                                                                                    <div class="video-box" style="height: 300px; background-image: url('.$videoImgUrl.'); background-repeat: no-repeat; background-position: center; background-size: cover;">
                                                                                                        <div class="video-btn">
                                                                                                            <a target="_blank" href="'.$video.'" class="show-effect"><span class="fa-sharp fa-solid fa-play"></span></a>
                                                                                                        </div>
                                                                                                    </div> 
                                                                                                    <span style="font-family:Rubik !important; font-style: normal; font-display: swap;" class="mb-10 mt-50">'.$name.'</span>                                                                       
                                                                                                </div>
                                                                                                

                                                                                                <!--div class="pp-content-grid-image pp-post-image">
                                                                                                    <div class="pp-post-featured-img">
                                                                                                        <div class="fl-photo fl-photo-crop-landscape fl-photo-align-center">
                                                                                                            <div class="fl-photo-content fl-photo-img-jpg">                                                                            
                                                                                                                '.$video.'
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                
                                                                                                <div class="pp-content-grid-inner pp-content-body clearfix"> 
                                                                                                    <div class="pp-content-post-data">
                                                                                                        <h3 style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;" class="pp-content-grid-title pp-post-title" itemprop="headline">
                                                                                                            '.$name.'
                                                                                                        </h3>
                                                                                                    </div>
                                                                                                </div-->';                                                    
                                                                                
                                                                                        }
                                                            
                                                                                    }  
                                                    

                                                                    $returnHTML .= '</div>
                                                                </div>
                                                            </section>
                                                        </div>

                                    </div>
                                </div>
                            
                        </div>
                    </div>
                </div>';                    
                $returnHTML .= '
            </div>
        </section><br><br>';
        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }


    public function galleryMain(){ 
        
        $returnHTML = $this->headerHTML();

        // $returnHTML .= '
        // <section>
        //     <div class="container">
        //         <div class="row">
        //             <div class="col-md-10" style="margin:0 auto;">';
        
        $returnHTML .='
        <section>
            <div class="container" style="min-width:80% !important; border:0px solid red;">                    
                <div class="row">
                    <div class="col-md-12" style="border:0px solid red;">

                        <div class="single-card card-style-one pl-2 mt-4 mb-3" style="border:0px solid red; padding-top:0px !important;">';

                                $returnHTML .= "
                                <div class=\"card-content\">
                                    <div class=\"row\">";                                            

                                        $returnHTML .= "<div class=\"col-md-3 order-md-1\">";
                                            
                                        $returnHTML .= $this->sidebarHTML();   

                                        $returnHTML .= "</div> "; 

                                        $returnHTML .= "<div class=\"row col-md-9\" style=\"border:0px solid red !important;\">"; 
                                        
                                                            $returnHTML .= '<!-- services Section -->
                                                            <section class="gallery-area section" style="padding-top:12px !important; background-image: -webkit-linear-gradient( 90deg, rgba(233, 247, 242, 0.4) 0%, rgb(233, 247, 242) 100% ) !important; padding: 2px 0 2px; width:100%; border:0px solid red !important; position:relative !important;">
                                                                <div class="container">

                                                                    <div class="section-title text-center">                                            
                                                                        <h2>Our Service Photo Gallery</h2>
                                                                    </div>';
                                                                    
                                                                        $gallerySql = "SELECT photo_gallery_id, name FROM photo_gallery WHERE photo_gallery_publish = 1 LIMIT 0, 6";
                                                                        $galleryObj = $this->db->getObj($gallerySql, array());
                                                                        if($galleryObj){ 
                                                                            $returnHTML .='
                                                                            <div class="row">                                            
                                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                                <div class="gallery-nav text-center">
                                                                                    <!--div class="row" style="border:0px solid red !important; margin:0 auto;"-->
                                                                                    <ul class="list-inline">
                                                                                        <li class="filter" data-filter="all">All</li>';
                                                                                                           
                                                                                        $picturesStr = '';                                        
                                                    
                                                                                        $returnHTML .= '';               
                                                                                        while($oneGalleryRow = $galleryObj->fetch(PDO::FETCH_OBJ)){
                                                                                            $photo_gallery_id = $oneGalleryRow->photo_gallery_id;
                                                                                            $name = stripslashes(trim((string) $oneGalleryRow->name));
                                                                                            $returnHTML .= '<li class="filter" data-filter=".id_'.$photo_gallery_id.'">'.$name.'</li>';                                                   
                                                                                        
                                                                                            $filePath = "./assets/accounts/photo_$photo_gallery_id".'_';
                                                                                            $pics = glob($filePath."*.jpg");
                                                                                            if(empty($pics) || !$pics){
                                                                                                $pics = glob($filePath."*.png");
                                                                                            }                            
                                                                                            if($pics){
                                                                                                // var_dump($pics);exit;
                                                                                                foreach($pics as $onePicture){
                                                                                                    $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                                                                                                    $photo_galleryImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                                                                                    
                                                                                                    $picturesStr .= '<div class="col-lg-3 col-md-6 col-xs-12 col-sm-12 mix id_'.$photo_gallery_id.'">
                                                                                                        <div class="gallery">
                                                                                                            <figure><a href="'.$photo_galleryImgUrl.'">
                                                                                                                <img alt="'.strip_tags(addslashes($name)).'" src="'.$photo_galleryImgUrl.'">
                                                                                                            <span></span>
                                                                                                        </a></figure>
                                                                                                        </div>
                                                                                                    </div>';
                                                    
                                                                                                    // $returnHTML .= '<div class="col-md-4" style="border:0px solid red !important; margin:0px !important; padding-bottom:20px !important; "><a href="#"><img src="'.$onePicture.'" alt="'.strip_tags(addslashes($name)).'" style="border:0px solid red;"></a></div>';
                                                    
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                        $returnHTML .= '</ul>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row" id="Container">'.$picturesStr.'</div>';
                                                    
                                                                                        $returnHTML .= '';
                                                                                                
                                                                                    
                                                                                

                                                                                    $returnHTML .= '
                                                                                    </ul>
                                                                                    <!--/div-->
                                                                                </div>
                                                                            </div>
                                                                        </div>';
                                                                    }

                                                                    
                                                                    $returnHTML .='</div>
                                                            </section>
                            
                                                        </div>
                                    </div>
                                </div>';                    
                                $returnHTML .= '
                        </div>
                    </div>
                </div>
            </div>
        </section><br><br>';
        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }
    

    public function servicesMain(){ 
        
        $returnHTML = $this->headerHTML();

        // $returnHTML .= '
        // <section>
        //     <div class="container">
        //         <div class="row">
        //             <div class="col-md-10" style="margin:0 auto;">';
        
        $returnHTML .='
        <section>
            <div class="container" style="min-width:80% !important; border:0px solid red;">                    
                <div class="row">
                    <div class="col-md-12" style="border:0px solid red;">

                        <div class="single-card card-style-one pl-2 mt-4 mb-3" style="border:0px solid red; padding-top:0px !important;">';

                        $returnHTML .= "
                        <div class=\"card-content\">
                            <div class=\"row\">";                                            

                                $returnHTML .= "<div class=\"col-md-3 order-md-1\">";
                                    
                                $returnHTML .= $this->sidebarHTML();   

                                $returnHTML .= "</div>  

                                <div class=\"row col-md-9\" style=\"border:0px solid red !important;\">
                               
                                <!--div class=\"col-md-12\">
                                        <h2 class=\"mb-10 fontdescription_two\"><strong>".$GLOBALS['title']."</strong></h2>
                                </diV>
                                <div class=\"col-md-12\" style=\"margin-top:20px;\">
                                    <p class=\"txtJustfy\"></p>
                                </diV-->";

                                $returnHTML .= '<!-- services Section -->
                                <!--section class="" style="border:0px solid red !important;">
                                    <div class="container">
                                        <div class="section-title">                                                        
                                            <h4 class="mb-10 fontdescription_two"><strong>Our Common Pest Control Service</strong></h4>
                                        </div>
                                        <div class="service-wrapper">
                                            
                                            <div class="row">';

                                                $tableObj = $this->db->getObj("SELECT name, font_awesome, uri_value, short_description FROM services WHERE services_publish =1 ORDER BY RAND() LIMIT 0, 6", array());
                                                if($tableObj){
                                                    while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                                        $name = trim(stripslashes($oneRow->name));
                                                        $font_awesome = trim(stripslashes($oneRow->font_awesome));
                                                        $uri_value = trim(stripslashes($oneRow->uri_value));
                                                        $short_description = trim(stripslashes($oneRow->short_description));

                                                        $returnHTML .= '<div class="col-md-6">
                                                            <div class="service-box">
                                                                <div class="icon">                                            
                                                                    <div class="'.$font_awesome.'"></div>                                            
                                                                </div>
                                                                <h5 class="text-center"><a style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;" href="/services/'.$uri_value.'.html">'.$name.'</a></h5>
                                                            </div>
                                                        </div>';
                                                    }
                                                }

                                            $returnHTML .= '
                                            </div>
                                        </div>
                                    </div>
                                </section-->';

                                $returnHTML .='
                                <section class="service-area" style="padding-top:30px !important; padding-bottom:210px !important;">
                                    <div class="container">
                                        <div class="section-title text-center">
                                            
                                            <h2>Common Pest Control</h2>
                                        </div>
                                        <div class="service-wrapper">
                                            <div class="row">';

                                                $tableObj = $this->db->getObj("SELECT name, font_awesome, uri_value, short_description FROM services WHERE services_publish =1 ORDER BY RAND()", array());
                                                if($tableObj){
                                                    while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                                        $name = trim(stripslashes($oneRow->name));
                                                        $font_awesome = trim(stripslashes($oneRow->font_awesome));
                                                        $uri_value = trim(stripslashes($oneRow->uri_value));
                                                        $short_description = trim(stripslashes($oneRow->short_description));

                                                        $returnHTML .= '<div class="col-md-6" style="margin-bottom:30px;">
                                                            <div class="service-box">
                                                                <div class="icon">                                            
                                                                    <div class="svgIcon '.$font_awesome.'"></div>                                            
                                                                </div>
                                                                <h4><a href="/services/'.$uri_value.'.html">'.$name.'</a></h4>
                                                                <div class="text">'.$short_description.'</div>
                                                                <div class="link-btn"><a href="/services/'.$uri_value.'.html" class="main-btn main-btn-one main-btn-two"><span>Learn More</span></a></div>
                                                            </div>
                                                        </div>';
                                                    }
                                                }

                                            $returnHTML .= '
                                            </div>
                                            
                                        </div>
                                    </div>
                                </section>';

                        $returnHTML .= '                            
                        </div>
                    </div>
                </div>';                    
                $returnHTML .= '
            </div>
        </section><br><br>';
        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }

    public function services(){
        
        $id = $GLOBALS['id'];

        if($id>0){

            $returnHTML = $this->headerHTML();

            $returnHTML .= '
            <section>
                <div class="container">                    
                    <div class="row">
                        <div class="col-md-12">
                        <div class="single-card card-style-one mt-30 mb-30">';
                    
                            $tablePageObj = $this->db->getObj("SELECT * FROM services WHERE services_id = $id", array());
                            if($tablePageObj){
                            
                                while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){

                                    $serviceImgUrl = ''; 
                                    $filePath = "./assets/accounts/serv_$id".'_';
                                    $pics = glob($filePath."*.jpg");
                                    if(!$pics){
                                        $pics = glob($filePath."*.png");
                                    }
                                    if($pics){
                                        foreach($pics as $onePicture){
                                            $serviceImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                        }
                                    }
                                    if(!empty($serviceImgUrl)){
                                        $serviceImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$serviceImgUrl\"  width=\"500\" height=\"500\" style=\"height:500px !important;\">";
                                    }
                                    else{
                                        $serviceImg = "<img alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\"  width=\"500\" height=\"500\" >";
                                    } 

                                   
                                    $returnHTML .="
                                    <div class=\"card-content\">
                                    <div class=\"row featurette\" >
                                        <div class=\"col-md-7 order-md-2\">
                                            <h2 class=\"card-title featurette-heading-new lh-1\">".stripslashes(trim((string) $onePageRow->name))."</h2>
                                            <br>
                                            <p class=\"text lead\">".nl2br(stripslashes(trim((string) $onePageRow->description)))."</p>
                                            
                                            <button class=\"btn btn-warning\" onclick=\"history.back()\">Go Back</button>
                                            <br><br>
                                        </div>
                                        <div class=\"card-image col-md-5 order-md-1\">
                                            <!--<svg class=\"bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto\" width=\"500\" height=\"500\" xmlns=\"http://www.w3.org/2000/svg\" role=\"img\" aria-label=\"Placeholder: 500x500\" preserveAspectRatio=\"xMidYMid slice\" focusable=\"false\"><title>Placeholder</title><rect width=\"100%\" height=\"100%\" fill=\"var(--bs-secondary-bg)\"/><text x=\"50%\" y=\"50%\" fill=\"var(--bs-secondary-color)\" dy=\".3em\">500x500</text></svg>-->
                                            $serviceImg
                                        </div>
                                    </div>
                                    </div>";    

                                
                                }
                            }
                            $returnHTML .= '
                            </div>
                        </div>
                    </div>';                    
                    $returnHTML .= '
                </div>
            </section>';
            $returnHTML .= $this->footerHTML();
        }
        else{
            $tableObj = $this->db->getObj("SELECT uri_value FROM pages WHERE pages_id = 1", array());
            $uri_value = $tableObj->uri_value;
            $returnHTML = "<meta http-equiv = \"refresh\" content = \"0; url = '/$uri_value.html'\" />";
        }
        
		return $returnHTML;
    }
    
    public function contactUs(){
        
        $contactUsPages = array(8=>array(), 9=>array(), 10=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($contactUsPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $contactUsPages[$oneRow->pages_id] = array(nl2br(stripslashes(trim((string) $oneRow->description))), $oneRow->uri_value);
            }
        }

        $returnHTML = $this->headerHTML();
        $returnHTML .= '
        <section class="contact-form-section" style="background-color:#E5F8F1;">
            <div class="container">
                <div class="pageTransBody">

                    <div class="row mt-5" style="border:0px solid red; margin-top:0px !important;">
                        <div class="col-sm-12">                             
                            <div class="row clearfix">

                                <!-- Reserve Box -->
                                <div class="reserve-box col-lg-4 col-md-5 col-sm-12">
                                    <div class="inner-box">
                                        <div class="content">
                                            <span class="icon flaticon-call">
                                            
                                            </span>
                                            <h4>Phone</h4>';
                                            if(!empty($contactUsPages[10])){
                                                $returnHTML .= $contactUsPages[10][0];
                                            }
                                        $returnHTML .= '</div>
                                    </div>
                                </div>
        
                                <!-- Reserve Box -->
                                <div class="reserve-box col-lg-3 col-md-4 col-sm-12 pleft0 pright0">
                                    <div class="inner-box">
                                        <div class="content">
                                            <span class="icon flaticon-email">
                                                
                                            </span>
                                            <h4>E-mail Us</h4>';
                                            if(!empty($contactUsPages[9])){
                                                $returnHTML .= $contactUsPages[9][0];
                                            }
                                        $returnHTML .= '<br><br><br></div>
                                    </div>
                                </div>
        
                                <!-- Reserve Box -->
                                <div class="reserve-box col-lg-5 col-md-4 col-sm-12">
                                    <div class="inner-box">
                                        <div class="content">
                                            <span class="icon flaticon-placeholder">
                                            
                                            </span>
                                            <h4>Address</h4>';
                                            if(!empty($contactUsPages[8])){
                                                $returnHTML .= $contactUsPages[8][0];
                                            }
                                        $returnHTML .= '<br><br></div>
                                    </div>
                                </div> 

                            </div>
                            

                            <!--Contact Form-->
                            
                            <!-- Sec Title -->
                            <br>
                            <div class="col-md-6 sec-title" style="border:0px solid red; margin-left:0px; margin-top:5px !important;margin-bottom:25px !important;">
                                <h2>Send Message</h2>
                                <span id="frmSubmitMessage" class="txt24 txtGreen"></span>
                            </div>

                            <div class="contact-form">
                                <form action="#" id="contactUsForm" onsubmit="sendContactUs(event)" style="border:0px solid red; margin:0 auto;">
                                    <div class="row">
                                        <!--<div class="form-group col-md-6">
                                            <div class="dropdown2 bootstrap-select2 dropup2">
                                                <select name="services_id" required>
                                                    <option value="">Select Service Type</option>';
                                                    
                                                    $tableObj = $this->db->getObj("SELECT services_id, name FROM services WHERE services_publish = 1 ORDER BY name ASC", array());
                                                    if($tableObj){
                                                        while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                                            $name = trim(stripslashes($oneRow->name));
                                                            $returnHTML .= "<option value=\"$oneRow->services_id\">$name</option>";
                                                        }
                                                    }
                                                    
                                                    $returnHTML .= '
                                                </select>
                                            </div>                            
                                        </div>-->

                                        <div class="form-group col-md-12">
                                            <input type="text" name="fname" placeholder="First Name *" required>
                                        </div> 
                                        <div class="form-group col-md-12">
                                            <input type="text" name="lname" placeholder="Last Name *" required>
                                        </div> 

                                        <div class="form-group col-md-6">
                                            <input type="email" name="email" placeholder="Email *" required>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <input type="text" name="phone" placeholder="Phone *" maxlength="10" required>
                                        </div>
                                        
                                        <div class="form-group col-md-12">
                                            <textarea class="minHeight100" name="note" placeholder="Message *" required></textarea>
                                        </div>                                        
                                        
                                        <div class="form-group col-md-12">
                                            <div id="mathCaptcha"></div>
                                            <span id="errRecaptcha" style="color:red"></span>
                                        </div>
                                                    
                                        <div class="form-group col-md-12">
                                            <div class="text-center">
                                                <button name="submit-form" type="submit" class="theme-btn btn-style-one"><span>Submit</span></button>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <span id="msgContact"></span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                               
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="/assets/js/mathCaptcha.js"></script>
        ';
        $returnHTML .= $this->footerHTML();

		return $returnHTML;
    }

    public function sendContactUs(){
        $POST = json_decode(file_get_contents('php://input'), true);
		$returnStr = '';
        
		// $email = addslashes($POST['email']??'');
		$email = array_key_exists('email', $_POST)?$_POST['email']:'';
        
		if($email =='' || is_null($email)){
            $returnStr = 'Could not send mail because of missing your email address.';
		}
		else{
			
			$fromName = trim(stripslashes((string) $_POST['fname']??''.' '.$_POST['lname']??''));
			$phone = nl2br(trim(stripslashes((string) $_POST['phone']??'')));
			$note = nl2br(trim(stripslashes((string) $_POST['note']??'')));            
			$subject = '[New message] From '.LIVE_DOMAIN." Contact Form";
			            
            
            $message = "<html>";
            $message .= "<head>";
            $message .= "<title>$subject</title>";
            $message .= "</head>";
            $message .= "<body>";
            $message .= "<p>";
            $message .= "Dear <i><strong>$fromName</strong></i>,<br />";
            $message .= "We received your request for contact.<br /><br />";
            $message .= "You wrote:<br />";
            $message .= "Phone: $phone<br>";
            $message .= "Email: $email<br>";
            $message .= "Message: $note";
            $message .= "</p>";
            $message .= "<p>";
            $message .= "<br />";
            $message .= "Thank you for contacting us.";
            $message .= "<br />";
            $message .= "We will reply as soon as possible.";
            $message .= "</p>";
            $message .= "</body>";
            $message .= "</html>";            

            $do_not_reply = $this->db->supportEmail('do_not_reply');
            $headers = array();
        
            $info = $this->db->supportEmail('info');
            $headers[] = "From: ".COMPANYNAME;
            $headers[] = "Reply-To: ".$do_not_reply;
            $headers[] = "Organization: ".COMPANYNAME;
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-type: text/html; charset=iso-8859-1";
            $headers[] = "X-Priority: 3";
            $headers[] = "X-Mailer: PHP".phpversion();

            
			if(mail($email, $subject, $message, implode("\r\n", $headers))){
				$returnStr = 'sent';
                
                $info = $this->db->supportEmail('info');
                $headersAdmin = array();
                $headersAdmin[] = "From: ".$email;
                $headersAdmin[] = "Reply-To: ".$do_not_reply;
                $headersAdmin[] = "Organization: ".COMPANYNAME;
                $headersAdmin[] = "MIME-Version: 1.0";
                $headersAdmin[] = "Content-type: text/html; charset=iso-8859-1";
                $headersAdmin[] = "X-Priority: 3";
                $headersAdmin[] = "X-Mailer: PHP".phpversion();                
                
                $message = "<html>";
                $message .= "<head>";
                $message .= "<title>$subject</title>";
                $message .= "</head>";
                $message .= "<body>";
                $message .= "<p>";
                $message .= "Dear Admin of <i><strong>".COMPANYNAME."</strong></i>,<br />";
                $message .= "We received a Contact request from $fromName.<br /><br />";
                $message .= "He / She wrotes:<br />";
                $message .= "Phone: $phone<br>";
                $message .= "Email: $email<br>";
                $message .= "Message: $note";
                $message .= "</p>";
                $message .= "<p>";
                $message .= "<br />";
                $message .= "Please reply him/her as soon as possible.";
                $message .= "</p>";
                $message .= "</body>";
                $message .= "</html>";
                
                mail($info, $subject, $message, implode("\r\n", $headersAdmin));
                
			}
			else{
				$returnStr = "Sorry! Could not send mail. Try again later.";
			}
		}
		return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
    }

    private function headerHTML(){
        
        $segment2URI = $GLOBALS['segment2URI']??'';
        
        $returnHTML = '';
        $title = $GLOBALS['title'];        

        $metaSiteName = $this->db->seoInfo('metaSiteName');
        $metaTitle = $this->db->seoInfo('metaTitle');
        if(in_array($segment2URI, array('home', 'null', ''))){$title = $metaTitle;}
        $metaDescription = $this->db->seoInfo('metaDescription');
        $metaKeyword = $this->db->seoInfo('metaKeyword');
        $metaDomain = $this->db->seoInfo('metaDomain');
        $metaUrl = $this->db->seoInfo('metaUrl');
        $metaImage = $this->db->seoInfo('metaImage');
        $metaVideo = $this->db->seoInfo('metaVideo');
        $metaLocale = $this->db->seoInfo('metaLocale');
        
                
		$htmlStr = '<!DOCTYPE html>
        <html lang="en">        
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
            <meta name="format-detection" content="telephone=no">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="language" content="English">
            
            <title>'.$title.'</title>
            <meta name="author" content="'.COMPANYNAME.'" />
            <meta name="title" content="'.$metaTitle.'"/>
            <meta name="description" content="'.$metaDescription.'"/>
            <meta name="keywords" content="'.$metaKeyword.'">
            <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large"/>

            <meta property="og:type" content="website" />
            <meta property="og:site_name" content="'.$metaSiteName.'"/>
            <meta name="og:domain" content="'.$metaDomain.'"/>
            <meta property="og:title" content="'.$metaTitle.'"/>
            <meta property="og:description" content="'.$metaDescription.'"/>';

            foreach($metaUrl as $oneMetaUrl=>$labelName){
                $htmlStr .= "<meta property=\"og:url\" content=\"$metaDomain$oneMetaUrl\"/>";
            }

            $htmlStr .= '<meta property="og:image" content="'.$metaImage.'"/>
            <meta property="og:image:type" content="image/png"/>
            <meta property="og:image:width" content="400"/>
            <meta property="og:image:height" content="300"/>
            <meta property="og:image:alt" content="'.$metaSiteName.'" />
            <meta content="'.$metaLocale.'" property="og:locale"/>
            <meta property="og:video" content="'.$metaVideo.'"/>
            <meta property="og:video:width" content="400"/>
            <meta property="og:video:height" content="300"/>
            <meta property="og:video:secure_url" content="'.$metaVideo.'"/>
            <meta property="og:video:type" content="application/x-shockwave-flash" />         

            <meta name="twitter:card" content="'.$metaDescription.'">
            <meta name="twitter:url" content="'.$metaDomain.'">
            <meta name="twitter:title" content="'.$metaTitle.'"/>
            <meta name="twitter:description" content="'.$metaDescription.'"/>
            <meta name="twitter:site" content="'.$metaSiteName.'"/>
            <meta name="twitter:image" content="'.$metaImage.'">
            <meta name="twitter:image:alt" content="'.$metaSiteName.'">
            <meta name="twitter:image:width" content="400"/>
            <meta name="twitter:image:height" content="300"/>
            <meta name="twitter:creator" content="'.COMPANYNAME.'">
            
            <link href="/website_assets/images/icons/favicon.ico" rel="shortcut icon">
            <link href="/website_assets/images/icons/favicon-32x32.png" rel="apple-touch-icon-precomposed">
            <link href="/website_assets/images/icons/favicon-16x16.png" rel="shortcut icon" type="image/png">
            
            <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&amp;family=Poppins:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet"> 
            <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;700&display=swap" rel="stylesheet"> 
            <link href="https://fonts.googleapis.com/css2?family=Montserrat Alternates:wght@300;400;700&display=swap" rel="stylesheet"> 
           
            <link rel="stylesheet" href="/website_assets/css/bootstrap.min.css">
            <link rel="stylesheet" href="/website_assets/css/animate.css">
            <link rel="stylesheet" href="/website_assets/css/style.css">   
            <link rel="stylesheet" href="/website_assets/css/responsive.css">              
        </head>
        <body>
        <div class="page-wrapper">';

            $headerPages = array(1=>array(), 2=>array());
            $tableObj = $this->db->getObj("SELECT pages_id, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($headerPages)).") AND pages_publish =1", array());
            if($tableObj){
                while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                    $headerPages[$oneRow->pages_id] = trim(stripslashes($oneRow->short_description));
                }
            }
            $htmlStr .='<header class="header';
            if(($segment2URI == "home") || ($segment2URI == null)){
                
            } else {
                 $htmlStr .=' otherPages';
            }
            $htmlStr .='">';
            $htmlStr .='
                <div class="header-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3 col-xs-0">
                                <div class="left-col">
                                    <div class="location"><i class="fa-solid fa-location-dot"></i> Find A Location</div>
                                    <div id="search-icon" class="search-btn">
                                        <button type="button" class="main-btn search-toggler"><i class="fa fa-search"></i></button>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-9 col-xs-12">
                                <div class="right-col">
                                    <ul class="contact-info">
                                        <li>Made to Exterminate: &nbsp;<a href="tel:'.$headerPages[1].'"><i class="fa-solid fa-phone"></i>'.$headerPages[1].'</a></li>
                                        <li><a href="mailto:'.$headerPages[2].'"><i class="fa-solid fa-envelope"></i>'.$headerPages[2].'</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="middle-header" id="middle-header">
                    <div class="header-upper" id="header-upper">
                        <div class="container">
                            <div class="inner-container desk-top-menu">
                                <div class="logo-box">
                                    <div class="logo"><a href="/"><img src="/website_assets/images/logo.png" alt=""></a></div>
                                </div>
                                <div class="nav-menu-all">
                                    <div class="mobile-nav-toggler" id="mobile-nav-toggler">
                                        <img src="/website_assets/images/icon-bar.png" alt="">
                                    </div>
                                    <nav class="main-menu navbar-expand-md navbar-light">
                                        <div class="collapse navbar-collapse show clearfix">
                                            <ul class="navigation">';
                                                $manuStr = $mobileManuStr = '';
                                                $activeYN = 0;
                                                $FMSql = "SELECT front_menu_id, name, menu_uri FROM front_menu WHERE root_menu_id  = 1 AND sub_menu_id = 0 AND front_menu_publish = 1 ORDER BY menu_position ASC";
                                                $FMObj = $this->db->getObj($FMSql, array());
                                                if($FMObj){
                                                    $currentURI = $GLOBALS['segment2URI']??'';
                                                    if(empty($currentURI)){$currentURI = '/';}
                                                    
                                                    while($oneRow = $FMObj->fetch(PDO::FETCH_OBJ)){
                                                        $sub_menu_id = $oneRow->front_menu_id;
                                                        $rootMame = trim(stripslashes($oneRow->name));
                                                        if($oneRow->menu_uri !='#'){
                                                            $rootMenu_uri = str_replace('//', '/', '/'.trim(stripslashes($oneRow->menu_uri))).'.html';
                                                        }
                                                        else{
                                                            $rootMenu_uri = 'javascript:void(0);';
                                                        }
                                                        if($rootMenu_uri=='/.html'){
                                                            $rootMenu_uri = '/';
                                                            $rootMame = '<i class="fa fa-home"></i> '.$rootMame;
                                                        }
                                                        $activeDefault = '';
                                                        if($currentURI==$oneRow->menu_uri){
                                                            $activeDefault = ' active';
                                                            $activeYN++;
                                                        }
                                                        
                                                        //==============Sub Menu============//
                                                        $FMSql2 = "SELECT name, menu_uri FROM front_menu WHERE root_menu_id  = 1 AND sub_menu_id = $sub_menu_id AND front_menu_publish = 1 ORDER BY menu_position ASC";
                                                        $FMObj2 = $this->db->getObj($FMSql2, array());
                                                        if($FMObj2){   
                                                            $manuStr .= "<li class=\"dropdown$activeDefault\">";
                                                            $mobileManuStr .= "<li class=\"dropdown$activeDefault\">";
                                                            if(strpos($rootMenu_uri, 'servi') !==-1){
                                                                $manuStr .= "<a style=\"font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;\" href=\"#\"><strong>$rootMame </strong><i class=\"fa fa-caret-down\"></i></a>";
                                                                $mobileManuStr .= "<a href=\"#\">$rootMame</a>";
                                                            } else {
                                                                $manuStr .= "<a href=\"$rootMenu_uri\">$rootMame <i class=\"fa fa-caret-down\"></i></a>";
                                                                $mobileManuStr .= "<a href=\"$rootMenu_uri\">$rootMame</a>";
                                                            }
                                                            $manuStr .= "<ul class=\"down-menu\">";
                                                            $mobileManuStr .= "<ul class=\"down-menu\" id=\"drop-menu\">";
                                                            while($oneRow2 = $FMObj2->fetch(PDO::FETCH_OBJ)){
                                                                $subName = trim(stripslashes($oneRow2->name));
                                                                $subMenuUri = trim(stripslashes($oneRow2->menu_uri));
                                                                $target = '';
                                                                if(strpos($subMenuUri, 'http') !== false){
                                                                    $target = ' target="_blank"';
                                                                }
                                                                else{
                                                                    $subMenuUri = str_replace('//', '/', '/'.trim(stripslashes($oneRow->menu_uri))).'.html';
                                                                }
                                                                $activeDefault = '';
                                                                if($currentURI==$oneRow2->menu_uri){
                                                                    $activeDefault = ' active';
                                                                    $activeYN++;
                                                                }
                                                                $manuStr .= "<li><a$target href=\"$subMenuUri\">$subName</a></li>";
                                                                $mobileManuStr .= "<li><a$target href=\"$subMenuUri\">$subName</a></li>";
                                                            }
                                                            $manuStr .= "</ul>
                                                            </li>";
                                                            $mobileManuStr .= "</ul>
                                                                <div class=\"dropdown-btn\" id=\"drop-btn\">
                                                                    <i class=\"fa fa-caret-down\"></i>
                                                                </div>
                                                            </li>";
                                                        }
                                                        else{
                                                            $manuStr .= "<li class=\"$activeDefault\"><a style=\"font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;\" href=\"$rootMenu_uri\"><strong>$rootMame</strong></a></li>";
                                                            $mobileManuStr .= "<li class=\"$activeDefault\"><a href=\"$rootMenu_uri\">$rootMame</a></li>";
                                                        }
                                                    }
                                                }

                                            $htmlStr .= $manuStr.'</ul>
                                        </div>
                                    </nav>
                                </div>
                                <div class="link-btn">
                                    <a href="/appointments.html" class="main-btn main-btn-one">
                                    <span>Appointment</span>
                                    <h6 style="font-size:12px; color:#00ff00;"><span>or free quote</span></h6>                                    
                                    </a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        
            <div class="mobile-menu">
                <div class="menu-backdrop"></div>
                <div id="close-btn" class="close-btn"><i class="fa-regular fa-circle-xmark"></i></div>
                <nav class="menu-box">
                    <div class="menu-inside">
                        <div class="container-menu">
                            <div class="nav-logo"><a href="#"><img src="/website_assets/images/logo-2.png" alt=""></a></div>
                            <div class="menu-outer">
                                <div>
                                    <ul class="navigation">
                                        '.$mobileManuStr.'
                                    </ul>
                                </div>
                            </div>
                            <div class="social-links">
                                <ul>
                                    <li><a target="_blank" href="https://twitter.com/PestControl0008/status/1637788217297752069"><i class="fab fa-twitter"></i></a></li>
                                    <li><a target="_blank" href="https://www.facebook.com/profile.php?id=100090470891383"><i class="fab fa-facebook-square"></i></a></li>
                                    <li><a target="_blank" href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                    <li><a target="_blank" href="https://www.youtube.com/channel/UC0AcRj2bwsDVhaEM_3WhphQ"><i class="fab fa-youtube"></i></a></li>  
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <section id="search-popup" class="search-popup">
                <div id="close-search" class="close-search"><i class="fa-regular fa-circle-xmark"></i></div>
                <div class="popup-inner">
                    <div class="overlay-layer"></div>
                    <div class="search-form">
                        <div class="form-group">';
                            $tableObj = $this->db->getObj("SELECT name, address, google_map, working_hours FROM branches WHERE branches_publish =1", array());
                            if($tableObj){
                                while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                    $htmlStr .= '<fieldset>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h2>'.trim(stripslashes($oneRow->name)).'</h2>
                                                <p>Working Hours: '.nl2br(trim(stripslashes($oneRow->working_hours))).'</p>
                                                <p>'.nl2br(trim(stripslashes($oneRow->address))).'</p>
                                            </div>
                                            <div class="col-md-6">
                                                '.trim(stripslashes($oneRow->google_map)).'
                                            </div>
                                        </div>
                                    </fieldset>';
                                }
                            }
                        $htmlStr .= '</div>
                    </div>
                </div>
            </section>
            ';

            if(!in_array($segment2URI, array('home', null, ''))){
                $htmlStr .='<section class="page-title">
                    <div class="container">                        
                        <div class="row">';
                            if(strlen($GLOBALS['title'])>28){
                                $htmlStr .='<div class="col-12" align="left">
                                    <h2 class="txtwhite" style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;">'.$GLOBALS['title'].'</h2>
                                </div>';
                            }
                            else{
                                $htmlStr .='<div class="col-6" align="left">
                                    <h2 class="txtwhite" style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;">'.$GLOBALS['title'].'</h2>
                                </div>
                                <div class="col-6">
                                    <ul class="breadcrumbs">
                                        <li class="breadcrumbs_item"><a href="/" style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;">Home</a></li>
                                        <li class="breadcrumbs_item active" aria-current="page"><a href="/" style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;">'.$GLOBALS['title'].'</a></li>
                                    </ul>
                                </div>';
                            }                
                        $htmlStr .='</div>
                    </div>
                </section>';           
            }
            
		return $htmlStr;
    }


    private function sidebarHTML(){
              
        $htmlStr = '';       
                
		$htmlStr = "
        <div class=\"nav animated bounceInDown bg-light\">
            <ul>";
            
            $contactUsPages = array(8=>array(), 9=>array(), 10=>array());
            $tableObj = $this->db->getObj("SELECT pages_id, description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($contactUsPages)).") AND pages_publish =1", array());
            if($tableObj){
                while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                    $contactUsPages[$oneRow->pages_id] = array(nl2br(stripslashes(trim((string) $oneRow->description))), $oneRow->uri_value);
                }
            }

            $manuStr = $mobileManuStr = '';
            $activeYN = 0;
            $FMSql = "SELECT front_menu_id, name, menu_uri FROM front_menu WHERE root_menu_id  = 13 AND sub_menu_id = 0 AND front_menu_publish = 1 ORDER BY menu_position ASC";
            $FMObj = $this->db->getObj($FMSql, array());
            if($FMObj){

                $currentURI = $GLOBALS['segment2URI']??'';
                if(empty($currentURI)){$currentURI = '/';}
                
                while($oneRow = $FMObj->fetch(PDO::FETCH_OBJ)){

                    $sub_menu_id = $oneRow->front_menu_id;
                    $rootName = trim(stripslashes($oneRow->name));
                    if($oneRow->menu_uri !='#'){
                        $rootMenu_uri = str_replace('//', '/', '/'.trim(stripslashes($oneRow->menu_uri))).'.html';
                    }
                    else{
                        $rootMenu_uri = 'javascript:void(0);';
                    }
                    
                    $activeDefault = '';
                    if($currentURI==$oneRow->menu_uri){
                        $activeDefault = ' active';
                        $activeYN++;
                    }
                    
                    //==============Sub Menu============//
                    $FMSql2 = "SELECT name, menu_uri FROM front_menu WHERE root_menu_id  = 13 AND sub_menu_id = $sub_menu_id AND front_menu_publish = 1 ORDER BY menu_position ASC";
                    $FMObj2 = $this->db->getObj($FMSql2, array());
                    if($FMObj2){   


                        // $manuStr .= "<li class=\"dropdown$activeDefault\">";
                        $manuStr .= "<li class=\"\">";
                        $mobileManuStr .= "<li class=\"dropdown$activeDefault\">";
        

                        // $manuStr .= "<a href=\"$rootMenu_uri\">$rootName <i class=\"fa fa-caret-down\"></i></a>";
                        $manuStr .= "<a href=\"#\">$rootName</a>";
                        $mobileManuStr .= "<a href=\"#\">$rootName</a>";

                        $manuStr .= "<ul>";
                        $mobileManuStr .= "<ul class=\"down-menu\" id=\"drop-menu\">";                                                               
                        

                        while($oneRow2 = $FMObj2->fetch(PDO::FETCH_OBJ)){
                            $subName = trim(stripslashes($oneRow2->name));
                            $subMenuUri = trim(stripslashes($oneRow2->menu_uri));
                            $target = '';
                            if(strpos($subMenuUri, 'http') !== false){
                                $target = ' target="_blank"';
                            }
                            else{
                                $subMenuUri = str_replace('//', '/', '/'.trim(stripslashes($oneRow->menu_uri))).'.html';
                            }
                            $activeDefault = '';
                            if($currentURI==$oneRow2->menu_uri){
                                $activeDefault = ' active';
                                $activeYN++;
                            }

                            // $manuStr .= "<li><a$target href=\"$subMenuUri\">$subName</a></li>";
                            $manuStr .= "<li><a class=\"bulletIcon\" href=\"$subMenuUri\">$subName</a></li>";
                            $mobileManuStr .= "<li><a$target href=\"$subMenuUri\">$subName</a></li>";

                        }
                        $manuStr .= "</ul>
                        </li>";

                        $mobileManuStr .= "</ul>
                            <div class=\"dropdown-btn\">
                                <i class=\"fa fa-caret-down\"></i>
                            </div>
                        </li>";

                    }
                    else{
                        // $manuStr .= "<li class=\"$activeDefault\"><a href=\"$rootMenu_uri\">$rootMame</a></li>";
                        $manuStr .= "<li class=\"$activeDefault\"><a href=\"$rootMenu_uri\">$rootMame</a></li>";
                        $mobileManuStr .= "<li class=\"$activeDefault\"><a href=\"$rootMenu_uri\">$rootMame</a></li>";
                    }
                }
            }

        $htmlStr .= $manuStr."</ul>
                
        </div>

        <section class=\"mt-40 elementor-section elementor-inner-section elementor-element elementor-element-5395d78 elementor-section-boxed elementor-section-height-default elementor-section-height-default\" data-id=\"5395d78\" data-element_type=\"section\" data-settings=\"{&quot;background_background&quot;:&quot;classic&quot;}\">
            <div class=\"elementor-background-overlay\"></div>
            <div class=\"elementor-container elementor-column-gap-no\">
                <div class=\"elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-5901866\" data-id=\"5901866\" data-element_type=\"column\">
                    <div class=\"elementor-widget-wrap elementor-element-populated\">
                        <div class=\"elementor-element elementor-element-5b4fa6a elementor-widget elementor-widget-heading\" data-id=\"5b4fa6a\" data-element_type=\"widget\" data-widget_type=\"heading.default\">
                        <div class=\"elementor-widget-container\">
                            <h4 class=\"elementor-heading-title elementor-size-default\">Have Any Question?</h4>
                        </div>
                        </div>
                        <div class=\"elementor-element elementor-element-88dcbd1 elementor-widget elementor-widget-text-editor\" data-id=\"88dcbd1\" data-element_type=\"widget\" data-widget_type=\"text-editor.default\">
                        <div class=\"elementor-widget-container\">
                            Contact us, our service man will give you support ASAP to move your live without pest problem. 						
                        </div>
                        </div>
                        <div class=\"elementor-element elementor-element-f3e8dc4 elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list\" data-id=\"f3e8dc4\" data-element_type=\"widget\" data-widget_type=\"icon-list.default\">
                        <div class=\"elementor-widget-container\">
                            <ul class=\"elementor-icon-list-items\">
                                <li class=\"elementor-icon-list-item\">
                                    <span class=\"elementor-icon-list-icon\">
                                    <i aria-hidden=\"true\" class=\"fas fa-phone-alt\"></i>						</span>
                                    <span class=\"elementor-icon-list-text\">".$contactUsPages[10][0]."</span>
                                </li>
                                <li class=\"elementor-icon-list-item\">
                                    <span class=\"elementor-icon-list-icon\">
                                    <i aria-hidden=\"true\" class=\"far fa-envelope\"></i>						</span>
                                    <span class=\"elementor-icon-list-text\"><a href=\"https://templatekit.jegtheme.com/cdn-cgi/l/email-protection\" class=\"__cf_email__\" data-cfemail=\"c4b7b1b4b4abb6b084a0aba9a5adaaeaa7aba9\">[email&#160;'".$contactUsPages[9][0]."']</a></span>
                                </li>
                            </ul>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>";

        return $htmlStr;

    }
	
    private function footerHTML(){	
        
        $headerPages = array(1=>array(), 2=>array(), 4=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($headerPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $headerPages[$oneRow->pages_id] = trim(stripslashes($oneRow->short_description));
            }
        }
        $location = '';
        $tableObj = $this->db->getObj("SELECT address FROM branches WHERE branches_publish=1 ORDER BY branches_id ASC LIMIT 0,1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $location = trim(stripslashes($oneRow->address));
            }
        }
		$htmlStr = '<!--Main Footer-->
        <footer class="footer-area">
            <div class="container">
                <div class="row">

                    <div class="col-lg-5 col-md-10">
                        <div class="footer-widget-content footer-top-content footer-links" style="border:0 solid red;">
                            <h4 class="footer-title">Gallery</h4>
                            <div class="images-gellary" style="border:0px solid red; width:90%;">';

                            $gallerySql = "SELECT photo_gallery_id, name FROM photo_gallery WHERE photo_gallery_publish = 1 LIMIT 0, 6";
                            $galleryObj = $this->db->getObj($gallerySql, array());
                            if($galleryObj){                        
                                $picturesStr = '';
                                $htmlStr .= '<ul>';               
                                while($oneGalleryRow = $galleryObj->fetch(PDO::FETCH_OBJ)){
                                    $photo_gallery_id = $oneGalleryRow->photo_gallery_id;
                                    $name = stripslashes(trim((string) $oneGalleryRow->name));
                                    
                                    $filePath = "./assets/accounts/photo_$photo_gallery_id".'_';
                                    $pics = glob($filePath."*.jpg");
                                    if(empty($pics) || !$pics){
                                        $pics = glob($filePath."*.png");
                                    }                            
                                    if($pics){
                                        foreach($pics as $onePicture){
                                            $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                                            $photo_galleryImgUrl = "//".OUR_DOMAINNAME.str_replace('./', '/', $onePicture);
                                            
                                            $htmlStr .= '<li><a href="#"><img src="'.$photo_galleryImgUrl.'" alt="'.strip_tags(addslashes($name)).'" style="width:120px !important; height:70px !important;"></a></li>';

                                        }
                                    }
                                }
                                $htmlStr .= '</ul>';
                                        
                            }

                            $htmlStr .='                                                              
                            </div>
                            <div class="row" style="width:100%; border:0 solid red; padding-left:20px;">
                                <a style="color:#FFFFFF; border:0px solid red; padding-lef:20px !important;padding-top:20px !important;" href="/gallery-main.html">more..</a>
                            </div>
                        </div>
                        
                    </div>

                    <div class="col-lg-4 col-md-5">
                        <div class="footer-top-content footer-links">
                            <h4 class="footer-title">Useful Link</h4>
                            <div class="footer-list-content">
                                <ul class="list">
                                    <li><a href="/about-pesterminate.html">About '.COMPANYNAME.'</a></li>
                                    <li><a href="/services-main.html">Services</a></li>
                                    <li><a href="/why-choose-us.html ">How It Works</a></li>
                                    <li><a href="/news-articles.html">News & Articles</a></li>
                                    <li><a href="/contact-us.html">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-7">
                        <div class="footer-top-content contact-widget">
                            <h4 class="footer-title">Contact</h4>
                            <ul>
                                <li>'.$location.'</li>
                                <li><a href="mailto:'.$headerPages[2].'">'.$headerPages[2].'</a></li>
                                <li><a href="tel:'.$headerPages[1].'">Made to Exterminate: <br>'.$headerPages[1].'</a></li>
                            </ul> 
                            <div class="footer-bottom" style="background-color:#0C1529 !important;">                        
                                <div class="footer-content">
                                    <ul class="social-icon">
                                        <li><a target="_blank" href="https://twitter.com/PestControl0008/status/1637788217297752069"><i class="fab fa-twitter"></i></a></li>
                                        <li><a target="_blank" href="https://www.facebook.com/profile.php?id=100090470891383"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a target="_blank" href="#"><i class="fab fa-google-plus-g"></i></a></li>
                                        <li><a target="_blank" href="https://www.youtube.com/channel/UC0AcRj2bwsDVhaEM_3WhphQ"><i class="fab fa-youtube"></i></a></li>
                                    </ul>
                                </div>
                            </div>                           
                        </div>                        
                    </div>
                </div>
            </div>
        </footer>
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-content">
                    <!--div class="logo"><a href="index.html"><img src="/website_assets/images/logo-2.png" alt=""></a></div-->
                    <div class="copyright" style="color:#B1B2B6;">Copyright ©'.date('Y').' <a target="_blank" href="https://skitsbd.com">SK IT SOLUTION</a>. All Rights Reserved.</div>
                    
                    <div class="footer-menu">
                        <ul>
                            <li><a href="/terms-of-service.html">Terms of Service</a></li>
                            <li><a href="/privacy-policy.html">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="scroll-top" onclick="topFunction()" id="scroll-top">
            <i class="fa fa-arrow-up" aria-hidden="true"></i>
        </div>

        <div class="fixedContactNo">
            <a href="tel:647-956-5868"><i class="fa fa-phone" aria-hidden="true"></i> 647-956-5868</a>
        </div>   

        <!-- jquery -->
        <script src="/website_assets/js/jquery-1.12.0.min.js"></script>
        <script src="/website_assets/js/bootstrap.min.js"></script>
        <script src="/website_assets/js/script.js"></script>
        
        </body>
        </html>';
		return $htmlStr;
	}
    
	function handleErr(){
		$POST = json_decode(file_get_contents('php://input'), true);

		$name = $POST['name']??'';
		if(is_array($name)){$name = implode(', ', $name);}
		$message = $POST['message']??'';
		if(is_array($message)){$message = implode(', ', $message);}
		$url = $POST['url']??'';
		if(is_array($url)){$url = implode(', ', $url);}

		$this->db->writeIntoLog($name . ', Message: '.$message . ', Page Url: '.$url);
		return json_encode(array('returnMsg'=>'Saved'));
	}	
}
?>