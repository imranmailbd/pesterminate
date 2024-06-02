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
                            <a href="https://homestars.com/companies/2883443-pesterminate-inc"><img src="/website_assets/images/winner-card.png" alt=""></a>
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
                            <a href="/services/residential.html" class="main-btn main-btn-one"><span> Find Services</span></a>
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
                    <div class="text-center mt-30 mb-30"><a href="/services/residential.html" class="main-btn main-btn-one"><span>More Services</span></a></div>
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
                                            $videoImgUrl = baseURL.str_replace('./', '/', $onePicture);
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
                                    $catImgSrc =baseURL.str_replace("./", '/', $onePicture);
                                }
                            }

                            $returnHTML .= '
                            <div class="col-lg-4 col-md-4">
                                <div class="blog-box">
                                    <div class="image"><a href="'.$uri_value.'.html"><img src="'.$catImgSrc.'" alt="" style="min-width:100%; max-height:100%;"></a></div>
                                    <h5 class="category" style="min-width:100%;">'.$name.'</h5>
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
                <div class="row">
                    <div class="col-md-4 order-md-1">';
                            
                        $returnHTML .= $this->sidebarHTML();   

                    $returnHTML .= '</div>
                    <div class="col-md-8">
                        <div class="card pd-height bg-color p-30">
                            <div class="title-part">
                                <h4>Do an <span>Appointment</span> To Us</h4>
                                <h3 style="color:#d00000">or <span>Get a</span> Free Quote</h3>
                            </div>
                            <!--Contact Form-->
                            <div class="contact-form">
                                <form action="#" id="frmAppointments" onsubmit="return saveAppointments(event);" style="border:0px solid red; margin:0 auto;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-input mt-15">
                                                <div class="input-items default">
                                                    <select name="services_id" id="services_id" required>
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
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input mt-15">
                                                <div class="input-items default">
                                                    <input type="text" name="email" id="email" placeholder="Your Email" required>
                                                    <i class="fa-solid fa-envelope text-normal"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-input mt-15">
                                                <div class="input-items default">
                                                    <input type="text" name="name" id="name" placeholder="Your Name" required>
                                                    <i class="fa fa-user"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-input mt-15">
                                                <div class="input-items default">
                                                    <input type="text" name="address" id="address" placeholder="Address" required>
                                                    <i class="fa fa-map-marker"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-input mt-15">
                                                <div class="input-items default">
                                                    <input type="text" name="phone" id="phone" placeholder="Phone" required>
                                                    <i class="fa fa-map-marker"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-input mt-15">
                                                <div class="input-items default">
                                                    <textarea name="description" id="description" placeholder="Description" required></textarea><i class="fas fa-massage fa-pen-to-square"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-15 col-md-12">
                                            <div id="mathCaptcha"></div>
                                            <span id="errRecaptcha" style="color:red"></span>
                                        </div>
                                        <div class="col-md-12">
                                            <span id="msgAppointments"></span>
                                            <div class="text-center">
                                                <input id="form_botcheck" name="form_botcheck" class="form-control" type="hidden" value="">
                                                <button type="submit" id="submitAppointments" class="theme-btn btn-style-one" data-loading-text="Please wait..."><span>Get Started</span></button>
                                            </div>
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
		$customerData['contact_no'] = $phone;
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
                    
                    /* $fromName = trim(stripslashes((string) $_POST['name']??''));
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
                    $headers[] = "X-Mailer: PHP".phpversion(); */   
	

                    $services_id = intval($_POST['services_id']??0);
                    $fromName = addslashes(trim($_POST['name']??''));
                    $email = addslashes(trim($_POST['email']??''));
                    $phone = addslashes(trim($_POST['phone']??''));
                    $note = addslashes(trim($_POST['description']??''));
                    $address = addslashes(trim($_POST['address']??''));	
                    $services_id = $_POST['services_id']??'';	
                    // $to = $email; //'user@example.com';                     
                    $from = $this->db->supportEmail('info');  // 'imran.skitsbd@gmail.com'; //  "imran@sksoftsolutions.ca";  // //'info@sksoftsolutions.ca'; 
                    // $fromName = COMPANYNAME;  //'SK SOFT SOLUTIONS Inc.'; 
                    $do_not_reply = $this->db->supportEmail('do_not_reply');                     
                    $subject = 'New appointment From '.LIVE_DOMAIN.' Contact Form'; 
                    $services_name = array(1=>'Cockroach', 2=>'Spiders', 3=>'Termites', 4=>'Rodents', 5=>'Fly Control', 6=>'Wasps Nest', 7=>'Ant Control', 8=>'Bed Bug');
                     
                    $message = ' 
                        <html> 
                        <head> 
                            <title>Welcome to '.COMPANYNAME.'</title> 
                        </head> 
                        <body> 
                            <h1>Dear <i><strong>'.$fromName.'</strong></i>,<br />Thanks you for joining with us! We received your request for appointment.<br /><br /></h1> 
                            <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
                                <tr>
                                    <th>You wrote:</th><td><br /></td> 
                                </tr>
                                <tr> 
                                    <th>Name:</th><td>'.$fromName.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Email:</th><td>'.$email.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Phone:</th><td>'.$phone.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Address:</th><td>'.$address.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Message:</th><td>'.$note.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Appointment No:</th><td>'.$appointments_id.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Service Name:</th><td>'.$services_name[$services_id].'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Thank you for contacting us.</th><td>We will reply as soon as possible.</td> 
                                </tr> 
                                <!--tr> 
                                    <th>Website:</th><td><a href="'.LIVE_DOMAIN.'">'.LIVE_DOMAIN.'</a></td> 
                                </tr--> 
                            </table> 
                        </body> 
                        </html>'; 
                     
                    // Set content-type header for sending HTML email 
                    $headers = "MIME-Version: 1.0" . "\r\n"; 
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
                     
                    // Additional headers 
                    $headers .= 'From: '.COMPANYNAME.'<'.$from.'>' . "\r\n"; 
                    // $headers .= 'Cc: imran@sksoftsolutions.ca' . "\r\n"; 
                    // $headers .= 'Bcc: imran@sksoftsolutions.ca' . "\r\n";                      
                    //#####################   

                    // var_dump($message);
                    // echo "<br>";
                    // var_dump($headers);
                    // echo "<br>";
                    // var_dump($email);exit;

                    // the message
                    // $msg = "First line of text\nSecond line of text";
                    // use wordwrap() if lines are longer than 70 characters
                    // $msg = wordwrap($msg,70);
                    // send email
                    // mail("imranmailbd@gmail.com","My subject",$msg);

                    
                    if(mail($email, $subject, $message, $headers)){
                        
                        $returnStr = 'sent';
                        
                        /* $info = $this->db->supportEmail('info');
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
                        $message .= "</html>";  */

                        
                        //####################################
                        $fromName = COMPANYNAME; 
                        $services_id = intval($_POST['services_id']??0);
                        $cname = addslashes(trim($_POST['name']??''));
                        $email = array_key_exists('email', $_POST)?$_POST['email']:'';
                        $phone = addslashes(trim($_POST['phone']??''));
                        $to = $this->db->supportEmail('info');  // 'imran.skitsbd@gmail.com';  ////   'imran.skitsbd@gmail.com';  //'user@example.com';    
                        $note = addslashes(trim($_POST['description']??''));    
                        $address = addslashes(trim($_POST['address']??''));	             
                        $from = $email; //$this->db->supportEmail('do_not_reply');  
                        // $fromName = COMPANYNAME;  //'SK SOFT SOLUTIONS Inc.'; 
                        $do_not_reply = $this->db->supportEmail('do_not_reply');                     
                        $subject = 'New appointment From '.LIVE_DOMAIN.' customer'; 
                        
                        $messageAdmin = ' 
                            <html> 
                            <head> 
                                <title>'.$subject.'</title> 
                            </head> 
                            <body> 
                                <h1>Dear Admin of <i><strong>'.COMPANYNAME.'</strong></i>,<br />We received a Appointment request from '.$cname.'.<br /><br /></h1> 
                                <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
                                    <tr>
                                        <th>Customer Information:</th><td><br /></td> 
                                    </tr>
                                    <tr> 
                                        <th>Name:</th><td>'.$cname.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Email:</th><td>'.$email.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Phone:</th><td>'.$phone.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Address:</th><td>'.$address.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Message:</th><td>'.$note.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Please reply him/her as soon as possible.</th><td>&nbsp;</td> 
                                    </tr>
                                </table> 
                            </body> 
                            </html>'; 
                      
                        // Set content-type header for sending HTML email 
                        $headersAdmin = "MIME-Version: 1.0" . "\r\n"; 
                        $headersAdmin .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
                        
                        // Additional headers 
                        $headersAdmin .= 'From: '.$cname.'<'.$email.'>' . "\r\n"; 
                        // $headersAdmin .= 'Cc: imran.skitsbd@gmail.com' . "\r\n"; 
                        // $headersAdmin .= 'Bcc: imran@sksoftsolutions.ca' . "\r\n";                      
                        //#####################   

                        // var_dump($messageAdmin);
                        // echo "<br>";
                        // var_dump($headersAdmin);
                        // echo "<br>";
                        // var_dump($to);exit;
                        
                        
                        mail($to, $subject, $messageAdmin, $headersAdmin);
                        
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

    

    public function pestservices(){        
        
        $id = 3;

        $bodyPages = array(1=>array(), 2=>array(), 3=>array(), 4=>array(), 5=>array(), 6=>array(), 7=>array(), 8=>array(), 9=>array(), 10=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, name, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($bodyPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $bodyPages[$oneRow->pages_id] = array(trim(stripslashes($oneRow->name)), trim(stripslashes($oneRow->short_description)));
            }
        }            
        $returnHTML = $this->headerHTML();

        $returnHTML .= '            
        <section style="padding:1px 0; background-color:#E5F8F1; z-index: -1;">
            <div class="container">                    
                <div class="single-card card-style-one pl-2 mt-4 mb-3" style="padding-top:0px !important;">';
                
                    $tablePageObj = $this->db->getObj("SELECT * FROM pages WHERE pages_id = $id", array());
                    if($tablePageObj){
                    
                        while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){

                            $pages_id = $onePageRow->pages_id;
                            $name = trim(stripslashes($onePageRow->name));
                            $page_create_date = date('m/d/Y', strtotime($onePageRow->created_on));
                            $uri_value = trim(stripslashes($onePageRow->uri_value));

                            // $pageImgUrl = ''; 
                            // $filePath = "./assets/accounts/srvn_$id".'_';
                            // $pics = glob($filePath."*.jpg");
                            // if(!$pics){
                            //     $pics = glob($filePath."*.png");
                            // }
                            
                            // if($pics){
                            //     foreach($pics as $onePicture){
                            //         $pageImgUrl =baseURL.str_replace('./', '/', $onePicture);
                            //     }
                            // }
                            // if(!empty($pageImgUrl)){
                            //     $pageImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$pageImgUrl\">";
                            // }
                            // else{
                            //     $pageImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\" >";
                            // } 

                        
                            $returnHTML .= "
                            <div class=\"card-content\">
                                <div class=\"row\">
                                    <div class=\"col-md-4 order-md-1\">";
                                        
                                        $returnHTML .= $this->sidebarHTML();   

                                        $returnHTML .= "
                                    </div>
                                    <div class=\"col-md-8\">
                                        <div class=\"row\">
                                            <div class=\"col-md-12 details\">";
                                            $prodObj = $this->db->getObj("SELECT pest_services_id AS id, name, description, price FROM pest_services WHERE pest_services_publish = 1", array());
                                            if($prodObj){
                                                while($oneRow = $prodObj->fetch(PDO::FETCH_OBJ)){
                                                    $returnHTML .= $this->productShortHTML($oneRow); 
                                                    // var_dump($oneRow);
                                                }
                                            }
                                            $returnHTML .= "
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
        </section>';
        $returnHTML .= $this->footerHTML();
        
		return $returnHTML;
    }

    
    public function productShortHTML($oneRow, $deals = 0){
        // var_dump($oneRow);exit;
        $baseURL = $GLOBALS['baseURL']??'';
        $customers_id = $_SESSION["customers_id"]??0;
        $currency = $_SESSION["currency"]??'$';
        $pest_services_id = $oneRow->id;               
        $service_name = trim(stripslashes($oneRow->name));
        $service_description = trim(stripslashes($oneRow->description));
        if(strlen($service_description)>100){
            $service_description = substr($service_description, 0, 100).'...';
        }
        $service_description = nl2br($service_description);
        $service_price = $oneRow->price;  
        $regularPriceStr = '';
        if($service_price>0){
            $regularPriceStr = '<span class="ps-product__del">$ '.$service_price.'</span><br>';
        }      
    
        //#################### Product Images ##########################
        
        $prodImg = 'pest.png';
        $defaultImageSRC = "website_assets/images/$prodImg";
        
        $prodPictures = array();
        $prodPictures[0] = array($prodImg, $defaultImageSRC);
        $prodPictures[1] = array($prodImg, $defaultImageSRC);
        
        $filePath = "./assets/accounts/srvn_$pest_services_id".'_';
        $pics = glob($filePath."*.jpg");
        if(!$pics){
            $pics = glob($filePath."*.png");
        }
        if($pics){
            $l = 0;
            foreach(array_slice($pics, 0, 2) as $onePicture){
                $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                $prodImgUrl = str_replace('./', '/', $onePicture);
                $prodPictures[$l] = array($prodImg, $prodImgUrl);
                $l++;
            }
        }
        //###########################################################

       
        $returnHTML = ''; 
        
        $returnHTML .= '<div class="shop-item inner-box row" style="border:1px solid #cccccc;">
            <div class="col-lg-3 col-md-4 col-sm-6 padding0">
                <div class="">
                    <img src="'.$prodPictures[0][1].'" alt="'.$prodPictures[0][0].'" style="width:80%;"/>
                </div>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-6 lower-content">
                <div class="row">
                    <div class="col-lg-9 col-md-8 txtleft">
                        <h4 class="mtop20">'.$service_name.'</h4>
                        <div>'.$service_description.'</div>
                    </div>
                    <div class="col-lg-3 col-md-4 padding0">
                        <div class="price" id="totalCalculatedPrice">
                            '.$regularPriceStr.'
                        </div>
                        <div class="w100Per" style="display:none;">
                            <div class="item-quantity">
                                <div class="quantity-spinner">
                                    <button type="button" class="minus">
                                        <span class="fa fa-minus"></span>
                                    </button>
                                    <input type="text" name="cartQty" id="cartQty'.$pest_services_id.'" value="1" min="1" max="9999" class="cartQty numberField">                                    
                                    <button type="button" class="plus">
                                        <span class="fa fa-plus"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="buttons-lower btn-group mtop10">
                            <input type="hidden" id="pest_services_id" name="pest_services_id" value="'.$pest_services_id.'">
                            <input type="hidden" id="pest_services_price" name="pest_services_price" value="'.$service_price.'">
                            <input type="hidden" id="pest_services_name" name="pest_services_name" value="'.$service_name.'">
                            <input type="hidden" id="returnYN" value="1">
                            <button type="button" title="ADD TO BUCKET" onclick="addToCart('.$pest_services_id.', \''.addslashes($service_name).'\', '.$service_price.',\''.$prodPictures[0][1].'\');" tabindex="0">
                                <i class=" fa fa-shopping-cart"></i>
                            </button>                          
                            
                            <button type="button" title="CHECKOUT FOR ORDER NOW" onclick="addToCart('.$pest_services_id.', \''.addslashes($service_name).'\', '.$service_price.',\''.$prodPictures[0][1].'\');" tabindex="0">
                                <i class=" fa fa-shopping-basket"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return $returnHTML;
    }



    public function set_sessionBranchesId(){

        $returnData = array();
		$returnData['login'] = '';
		$returnData['error'] = '';
		$returnData['branches_id'] = 0;

        $POST = json_decode(file_get_contents('php://input'), true);
        $branches_id = intval($POST["branches_id"]??0);

        // echo ($branches_id);exit;

        $tableObj = $this->db->getObj("SELECT branches_id, weekday_pickup_start, weekday_pickup_end FROM branches WHERE branches_id = $branches_id", array());
        if($tableObj){
            
            $tableRow = $tableObj->fetch(PDO::FETCH_OBJ);
            // var_dump($tableRow);

            $branches_id = intval($tableRow->branches_id);
            $weekday_pickup_start = intval($tableRow->weekday_pickup_start);
            $weekday_pickup_end = intval($tableRow->weekday_pickup_end);
            if($weekday_pickup_end<=2){
                $weekday_pickup_end += 24;
            }
            $currentHour = date('H');
            if($currentHour<=2){
                $currentHour += 24;
            }
            if($currentHour<$weekday_pickup_start || $weekday_pickup_end<$currentHour){
                $pickupStr = '';
				if($weekday_pickup_start<12){
					if($weekday_pickup_start==0){$weekday_pickup_start = 12;}
					$pickupStr .= "$weekday_pickup_start AM";
				}
				else{
					$weekday_pickup_start -= 12;
					if($weekday_pickup_start==0){$weekday_pickup_start = 12;}
					$pickupStr .= "$weekday_pickup_start PM";
				}
				$pickupStr .= ' to ';
				if($weekday_pickup_end<12){
					if($weekday_pickup_end==0){$weekday_pickup_end = 12;}
					$pickupStr .= "$weekday_pickup_end AM";
				}
				else{
					$weekday_pickup_end -= 12;
					if($weekday_pickup_end==0){$weekday_pickup_end = 12;}
					$pickupStr .= "$weekday_pickup_end PM";
				}

                $returnData['error'] = "Please make sure your pickup time will be $pickupStr";
            }
            $returnData['branches_id'] = $branches_id;
        }
        $_SESSION["branches_id"] = $returnData['branches_id'];

        var_dump($returnData);exit;

        return json_encode($returnData);
    }


    public function my_Order(){
        
        $payment_ref_id = $statusMsg = ''; 
        $status = 'error'; 
        
        if(isset($_SESSION["clientSecret"])){
            unset($_SESSION["clientSecret"]);
        }
        if(isset($_SESSION["paymentIntentId"])){
            unset($_SESSION["paymentIntentId"]);
        }
        if(isset($_SESSION["price"])){
            unset($_SESSION["price"]);
        }
        if(isset($_SESSION["branches_id"])){
            unset($_SESSION["branches_id"]);
        } 
        if(isset($_SESSION["customers_id"])){
            unset($_SESSION["customers_id"]);
        } 

        $returnHTML = $this->headerHTML();
        $returnHTML .= '<section class="othersBody">
            <div class="container">                
                <div class="row" id="capture">
                    <div class="col-12 col-lg-4 pbottom10">
                        <table width="100%" align="left" class="tableBorder" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th style="text-align: center;line-height: 30px;" class="bbottom" id="readyForPickup" colspan="7">Customer Information:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="timerColumn">
                                                    <div class="number">
                                                        <span id="minutes"><b>Customer Name</b></span>
                                                    </div> 
                                                    <div class="number">
                                                        <span id="customer_name_conf">&nbsp;</span>
                                                    </div>                                                   
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="timerColumn">
                                                    <div class="number">
                                                        <span id="seconds"><b>Customer Email</b></span>
                                                    </div>  
                                                    <div class="number">
                                                        <span id="customer_email_conf">&nbsp;</span>
                                                    </div>                                                  
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            
                        </table>
                        <table width="100%" align="left" class="tableBorder" style="margin-top:10px;">
                            <thead>
                                <tr>
                                    <th style="text-align: center;line-height: 30px;" class="bbottom" id="readyForTrack" colspan="7">Your Order Tracking No.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="timerColumn row" style="font-size: 35px; padding:20px;">
                                                    <div class="number col-4 text-right">
                                                        <span id="minutes"><b>#</b></span>
                                                    </div> 
                                                    <div class="number col-8">
                                                        <span id="customer_order_track_no_conf" class="text-center font-weight-bold">&nbsp;</span>
                                                    </div>                                                   
                                                </div>
                                            </div>                                            
                                        </div>
                                    </td>
                                </tr>
                            </tbody>                            
                        </table>
                        <table width="100%" align="left" class="tableBorder mtop10">
                            <thead>
                                <tr>
                                    <th class="bbottom" colspan="7">Expected Service Branch</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="addressInformation">
                                    </td>
                                </tr>
                            </tbody>
                        </table>                        
                    </div>
                    <div class="col-12 col-lg-8 pbottom10" id="myOrderCarts"></div>                        
                </div>
            </div>
        </section>
        <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>';
        $returnHTML .= $this->footerHTML();

		return $returnHTML;
	}


    public function getPOSInfo(){
        
        $POST = json_decode(file_get_contents('php://input'), true);
        
        $message = 'Sorry, could not check Phone number.';
        $savemsg = 'error';
        $newCustomerData = $newPOSData = $newBranchesData = array();
        $id = 0;
        $pos_id = intval(array_key_exists('pos_id', $POST)?$POST['pos_id']:0);
        $error = '';
        if($pos_id == 0){
            $message = 'Your cart information has been expired. Please create new Order.';
            $error = 'Error';
        }
        if(empty($error)){
            $baseURL = $GLOBALS['baseURL']??'';
            
            $posObj = $this->db->getObj("SELECT * FROM pos WHERE pos_id = :pos_id", array('pos_id'=>$pos_id));
            if($posObj){
                while($posRow = $posObj->fetch(PDO::FETCH_OBJ)){
                    
                    $customersObj = $this->db->getObj("SELECT * FROM customers WHERE customers_id = :customers_id AND customers_publish = 1", array('customers_id'=>$posRow->customers_id));
                    if($customersObj){
                        while($oneRow = $customersObj->fetch(PDO::FETCH_OBJ)){
                            $newCustomerData = (array)$oneRow;
                        }
                    }

                    $branchesObj = $this->db->getObj("SELECT * FROM branches WHERE branches_id = :branches_id AND branches_publish = 1", array('branches_id'=>$posRow->branches_id));
                    if($branchesObj){
                        while($oneRow = $branchesObj->fetch(PDO::FETCH_OBJ)){
                            $newBranchesData = array('name'=>stripslashes(trim($oneRow->name)), 'address'=>nl2br(stripslashes(trim($oneRow->address))), 'working_hours'=>nl2br(stripslashes(trim($oneRow->working_hours))), 'google_map'=>stripslashes(trim($oneRow->google_map)));
                        }
                    }

                    $cartData = array();
                    $cartObj = $this->db->getObj("SELECT * FROM pos_cart WHERE pos_id  = :pos_id", array('pos_id'=>$posRow->pos_id));
                    if($cartObj){
                        while($cartRow = $cartObj->fetch(PDO::FETCH_OBJ)){
                            $cartCMOData = array();
                            $cartCMOObj = $this->db->getObj("SELECT * FROM pos_cart_cmo WHERE pos_cart_id  = :pos_cart_id", array('pos_cart_id'=>$cartRow->pos_cart_id));
                            if($cartCMOObj){
                                while($cartCMORow = $cartCMOObj->fetch(PDO::FETCH_OBJ)){
                                    $cartCMOData[$cartCMORow->pos_cart_cmo_id] = (array)$cartCMORow;
                                }
                            }

                            //########################################################
                            // $prodImg = 'pest.png';
                            // $defaultImageSRC = "website_assets/images/$prodImg";
                            
                            // $prodPictures = array();
                            // $prodPictures[0] = array($prodImg, $defaultImageSRC);
                            // $prodPictures[1] = array($prodImg, $defaultImageSRC);
                            
                            // $filePath = "./assets/accounts/srvn_$pest_services_id".'_';
                            // $pics = glob($filePath."*.jpg");
                            // if(!$pics){
                            //     $pics = glob($filePath."*.png");
                            // }
                            // if($pics){
                            //     $l = 0;
                            //     foreach(array_slice($pics, 0, 2) as $onePicture){
                            //         $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                            //         $prodImgUrl = str_replace('./', '/', $onePicture);
                            //         $prodPictures[$l] = array($prodImg, $prodImgUrl);
                            //         $l++;
                            //     }
                            // }
                            //########################################################
                            
                            $product_id = $cartRow->item_id;
                            $prodImg = 'pest.png';
                            $defaultImageSRC = "$baseURL/website_assets/images/$prodImg";
                            
                            $prodPictures = array();
                            $prodPictures[0] = array($prodImg, $defaultImageSRC);
                            $prodPictures[1] = array($prodImg, $defaultImageSRC);
                            
                            $filePath = "./assets/accounts/srvn_$product_id".'_';
                            $pics = glob($filePath."*.jpg");
                            if(!$pics){
                                $pics = glob($filePath."*.png");
                            }
                            if($pics){
                                $l = 0;
                                foreach(array_slice($pics, 0, 2) as $onePicture){
                                    $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                                    $prodImgUrl = str_replace('./', '/', $onePicture);
                                    $prodPictures[$l] = array($prodImg, $prodImgUrl);
                                    $l++;
                                }
                            }
                            
                            $cartRow1 = array('pos_id'=>$cartRow->pos_id,
                                            'item_id'=>$cartRow->item_id,
                                            'item_type'=>$cartRow->item_type,
                                            'description'=>$cartRow->description,
                                            'choice_more'=>$cartRow->choice_more,
                                            'add_description'=>$cartRow->add_description,
                                            'sales_price'=>$cartRow->sales_price,
                                            'qty'=>$cartRow->qty,
                                            'shipping_qty'=>$cartRow->shipping_qty,
                                            'return_qty'=>$cartRow->return_qty,
                                            'ave_cost'=>$cartRow->ave_cost,
                                            'discount_is_percent'=>$cartRow->discount_is_percent,
                                            'discount'=>$cartRow->discount,
                                            'taxable'=>$cartRow->taxable,
                                            'prodPictures'=>$prodPictures
                                        );
                            $cartData[$cartRow->pos_cart_id] = array('cartRow'=>$cartRow1, 'cartCMOData'=>$cartCMOData);
                        }
                    }
                    $newPOSData = array('posData'=>(array)$posRow, 'cartData'=>$cartData);
                }
            }
        }

        // var_dump($newPOSData);exit;

        return json_encode(array('login'=>'', 'posData'=>$newPOSData, 'customerData'=>$newCustomerData, 'branchesData'=>$newBranchesData));
    }


    public function checkRegistered(){

        $branches_id = $_SESSION["branches_id"]??1;
        $POST = json_decode(file_get_contents('php://input'), true);
        // var_dump($POST);

        $returnData = array();
		$returnData['login'] = '';
		$returnData['message'] = '';
		$returnData['savemsg'] = 'error';
		$returnData['branches_id'] = 0;
		$returnData['customers_id'] = 0;
        $returnData['stripe_client_secret'] = '';
        $returnData['paymentIntentId'] = '';

        $branches_id = intval(array_key_exists('branches_id', $POST)?$POST['branches_id']:$branches_id);
        $_SESSION["branches_id"] = $branches_id;
		$returnData['branches_id'] = $branches_id;

        $name = trim((string) array_key_exists('name', $POST)?$POST['name']:'');
        $contact_no = trim((string) array_key_exists('phone_number', $POST)?$POST['phone_number']:'');
        $expct_date = trim((string) array_key_exists('expct_date', $POST)?$POST['expct_date']:'');
        
        $email = trim((string) array_key_exists('email', $POST)?$POST['email']:'');
        $clientSecret = trim((string) array_key_exists('clientSecret', $POST)?$POST['clientSecret']:'');
        $paymentIntentId = trim((string) array_key_exists('paymentIntentId', $POST)?$POST['paymentIntentId']:'');
        $price = round(floatval(array_key_exists('amount', $POST)?$POST['amount']:0.00),2);
        
        $stripeCall = 0;

        // var_dump($_SESSION["clientSecret"]);exit;

        if(!isset($_SESSION["clientSecret"]) || $_SESSION["clientSecret"] != $clientSecret){
            $_SESSION["clientSecret"] = $clientSecret;
            $stripeCall++;
        }
        if(!isset($_SESSION["paymentIntentId"]) || $_SESSION["paymentIntentId"] != $paymentIntentId){
            $_SESSION["paymentIntentId"] = $paymentIntentId;
            $stripeCall++;
        }
        if(!isset($_SESSION["price"]) || $_SESSION["price"] != $price){
            $_SESSION["price"] = $price;
            $stripeCall++;
        }

        $error = '';
        if($name=='' || strlen($name)<4){
            $message = 'Name should be min 4 characters.';
            $error = 'Error';
        }
        elseif(strlen($name)>50){
            $message = 'Name should be max 50 characters';
            $error = 'Error';
        }

        if(strlen($contact_no)<9 || strlen($contact_no)>15){
            $message = 'Invalid Phone number.';
            $error = 'Error';
        }

        if($email=='' || strlen($email)<6){
            $message = 'Email should be min 6 characters.';
            $error = 'Error';
        }
        elseif(strlen($email)>50){
            $message = 'Email should be max 50 characters';
            $error = 'Error';
        }

        // if($expct_date=='' || strlen($expct_date)<6){
        //     $message = 'Expected date required.';
        //     $error = 'Error';
        // }
        // elseif(strlen($expct_date)>15){
        //     $message = 'Expected date should be max 15 characters';
        //     $error = 'Error';
        // }

        
        if(empty($error)){

            $sqlCustomers = "SELECT customers_id FROM customers WHERE contact_no = :contact_no AND email = :email";
            $tableObj = $this->db->getObj($sqlCustomers, array('contact_no'=>$contact_no, 'email'=>$email));
            if($tableObj){
                $customers_id = intval($tableObj->fetch(PDO::FETCH_OBJ)->customers_id);
                if($customers_id>0){
                    $this->db->update('customers', array('name'=>$name,'email'=>$email,'phone'=>$contact_no,'branches_id'=>$branches_id,'customers_publish'=>1), $customers_id);
                    $returnData['customers_id'] = $customers_id;
                    $returnData['savemsg'] = 'Old';
                }
            }

            if($returnData['customers_id']==0){
                $customersdata = array( 'customers_publish'=>1,
                                'created_on' => date('Y-m-d H:i:s'),
                                'last_updated' => date('Y-m-d H:i:s'),
                                'users_id'=>0,
                                'name'=>$name,
                                'phone'=>$contact_no,
                                'email'=>$email,
                                'address'=>'n/a',
                                'offers_email'=>0,
                                'company'=>'n/a',
                                'contact_no'=>$contact_no,
                                'secondary_phone'=>'',                                
                                'fax'=>'',                                
                                'shipping_address_one'=>'',
                                'shipping_address_two'=>'',
                                'shipping_city'=>'',
                                'shipping_state'=>'',
                                'shipping_zip'=>'',
                                'shipping_country'=>'',
                                'custom_data'=>'',
                                'alert_message'=>'',
                                'branches_id'=>$branches_id,
                                'sales_tax_name'=>'',
                                'sales_tax_rate'=>0.00
                                );
                $customers_id = $this->db->insert('customers', $customersdata);

                // var_dump($customersdata);exit;

                if($customers_id){
                    $returnData['savemsg'] = 'Add';
                    $returnData['customers_id'] = $customers_id;
                }
            }

            if($returnData['customers_id']>0){
                $_SESSION["customers_id"] = $customers_id;
                // var_dump($_SESSION["customers_id"]);exit;
                // if($stripeCall>0){
                    // var_dump('stripe');exit;
                    $Stripe = new Stripe($this->db);
                    // var_dump('stripe2');exit;
                    $getPaymentIntentData = $Stripe->getPaymentIntent($customers_id, $price);
                    $this->db->writeIntoLog("getPaymentIntentData:".json_encode($getPaymentIntentData));
                    $_SESSION["clientSecret"] = $getPaymentIntentData['stripe_client_secret'];
                    $_SESSION["paymentIntentId"] = $getPaymentIntentData['paymentIntentId'];                    
                // }
                // var_dump('no stripe');exit;

                $returnData['stripe_client_secret'] = $_SESSION["clientSecret"];
                $returnData['paymentIntentId'] = $_SESSION["paymentIntentId"];
            }
        }
        else{
            $returnData['message'] =  $message;
		    $returnData['savemsg'] = 'error';
        }

        return json_encode($returnData);
    }


   
    public function checkout(){

        $customers_id = $_SESSION["customers_id"]??0;
        $branches_id = $_SESSION["branches_id"]??0;
        // var_dump($_SESSION);exit;
        
        $first_name = $email = $contact_no = '';
        if($customers_id>0){
            $customersObj = $this->db->getObj("SELECT name, email, phone, branches_id FROM customers WHERE customers_id = $customers_id", array());
            if($customersObj){
                while($oneRow = $customersObj->fetch(PDO::FETCH_OBJ)){
                    $first_name = $oneRow->name;
                    $email = $oneRow->email;
                    $contact_no = $oneRow->contact_no;
                    if($branches_id==0){
                        $branches_id = $oneRow->branches_id;
                        $_SESSION["branches_id"] = $branches_id;
                    }
                }
            }
        }

        $returnHTML = $this->headerHTML();
        
        $baseURL = $GLOBALS['baseURL']??'';
        $returnHTML .= '<section class="othersBody">
            <div class="container">
                
                <form action="/confirmCheckOut" id="confirmCheckOut" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-12 col-lg-8 ptop15 pbottom10" id="checkOutCarts"></div>
                        <div class="col-12 col-lg-4 ptop15 pbottom10">
                            <div class="w100Per">
                                <span class="errorMsg lineHeight30 padding20" style="display:none" id="error_form"></span>
                                <span class="successMsg lineHeight30 padding20" style="display:none" id="success_form"></span>
                            </div>
                            <div class="page-body checkout-data">
                                <ol class="checkoutSteps">

                                    <!-- Service Loication  Checkout Right Bar Start -->
                                    <li id="firstStep" class="tab-section allow active">
                                        <div class="step-title">
                                            <div class="step-title-info cursor" onclick="checkOutStepsUpDown(\'firstStep\', 1)">
                                                <span class="check"><i class="fa fa-check"></i></span>
                                                <span class="number">1.</span>
                                                <h2 class="title mbottom0">Service Area</h2>
                                            </div>
                                        </div>
                                        <div class="step-details">
                                            <div class="page-body">
                                                <div class="col-12">';
                                                    $tableObj = $this->db->getObj("SELECT branches_id, name, address FROM branches WHERE branches_publish =1", array());
                                                    if($tableObj){
                                                        while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                                            $checked = '';
                                                            if($branches_id==$oneRow->branches_id){
                                                                $checked = ' checked="checked"';
                                                            }
                                                            $returnHTML .= '<div class="inputs">
                                                                <label class="cursor pleft20" for="branches_'.$oneRow->branches_id.'">
                                                                <p><input'.$checked.' type="radio" class="radioBtn" style="margin-left:-35px" id="branches_'.$oneRow->branches_id.'" name="branches_id" value="'.$oneRow->branches_id.'"> '.trim(stripslashes($oneRow->name)).nl2br(strip_tags(trim(stripslashes($oneRow->address)))).'</p>
                                                                </label>
                                                            </div>';

                                                        }
                                                    }

                                                    $returnHTML .= '<div class="inputs">
                                                        <label for="expct_date"><b>Expected Date</b>: <br>
                                                        <span style="font-size:12px;color:#248ECE;">Choose your expected service date</span></label>
                                                        <input type="text" class="form-control height30 lineHeight30 DateField expct_date" id="expct_date" name="expct_date">
                                                        <!--span class="txtred" id="error_expct_date"></span-->
                                                    </div>';                                                
                                                    

                                                    $returnHTML .= '<div class="buttons">
                                                        <button type="button" class="button-2 proceed-button" onclick="checkBranches(1)">Proceed</button>                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- Service Provide Loication  Checkout Right Bar End -->

                                   
                                    <!-- Your Basic Info  Checkout Right Bar Start -->
                                    <li id="secondStep" class="tab-section">
                                        <div class="step-title">
                                            <div class="step-title-info cursor" onclick="checkOutStepsUpDown(\'secondStep\', 1)">
                                                <span class="check"><i class="fa fa-check"></i></span>
                                                <span class="number">2.</span>
                                                <h2 class="title mbottom0">Your Basic Info</h2>
                                            </div>
                                        </div>
                                        <div class="step-details" style="display:none">
                                            <div class="page-body">
                                                <div class="col-12" id="basicInformation">
                                                    <div class="inputs">
                                                        <label for="first_name">Name: <span class="txtred">*</span></label>
                                                        <input type="text" class="form-control height30 lineHeight30" required="required" id="name" name="name" value="'.$first_name.'">
                                                        <span class="txtred" id="error_name"></span>
                                                    </div>
                                                    <div class="inputs">
                                                        <label for="phone_number">Enter Phone Number:</label>
                                                        <input type="tel" class="form-control height30 lineHeight30" autofocus="autofocus" id="phone_number" name="phone_number" onKeyup="checkPhone(this, 0)" value="'.$contact_no.'">
                                                        <span class="txtred" id="error_phone_number"></span>
                                                    </div>
                                                    <div class="inputs">
                                                        <label for="email">Email: <span class="txtred">*</span></label>
                                                        <input type="email" class="form-control height30 lineHeight30" id="email" name="email" value="'.$email.'">
                                                        <span class="txtred" id="error_email"></span>
                                                    </div>
                                                    <div class="buttons">
                                                        <button type="button" class="button-2 proceed-button" onclick="checkRegistered()">Proceed</button>                    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- Your Basic Info  Checkout Right Bar End -->
                                    

                                    <!-- Payment Method  Checkout Right Bar Start -->
                                    <li id="thirdStep" class="tab-section">
                                        <div class="step-title">
                                            <div class="step-title-info cursor" onclick="checkOutStepsUpDown(\'thirdStep\', 1)">
                                                <span class="check"><i class="fa fa-check"></i></span>
                                                <span class="number">3.</span>
                                                <h2 class="title mbottom0">Payment Method</h2>
                                            </div>
                                        </div>
                                        <div class="step-details" style="display:none">
                                            <input type="hidden" name="amount_due" id="amount_due" value="0">
                                             <script src="https://js.stripe.com/v3/"></script>
                                             <input type="hidden" name="amount_due" id="amount_due" value="0">
                                            <div class="page-body">
                                                <div class="col-12">';

                                                    $clientSecret = $_SESSION["clientSecret"]??'';
                                                    $paymentIntentId = $_SESSION["paymentIntentId"]??'';

                                                    $returnHTML .= '<input type="hidden" name="clientSecret" id="clientSecret" value="'.$clientSecret.'">
                                                    <input type="hidden" name="paymentIntentId" id="paymentIntentId" value="'.$paymentIntentId.'">
                                                
                                                    <div id="paymentElement"></div>
                                                    <div class="buttons">
                                                        <button disabled id="submitBtn" class="btn btn-success flex gap-2">
                                                            <div class="spinner hidden" id="spinner"></div>
                                                            <span id="buttonText">Pay Now</span>
                                                        </button>                                                            
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- Payment Method  Checkout Right Bar End -->


                                </ol>
                            </div>
                        </div>
                    </div>
                </form>
                
                <div id="frmProcess" class="hidden">
                    <span class="ring"></span> Processing...
                </div>
                <!-- Display re-initiate button -->
                <div id="payReinit" class="hidden">
                    <button class="btn btn-primary" onClick="window.location.href=window.location.href.split(\'?\')[0]"><i class="rload"></i>Re-initiate Payment</button>
                </div>
                ';
                $stripe_pkData = $pickupHoursData = array();
                $tableObj = $this->db->getObj("SELECT branches_id, stripe_pk, weekday_pickup_start, weekday_pickup_end FROM branches", array());
                if($tableObj){
                    while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                        $stripe_pkData[$oneRow->branches_id] = $oneRow->stripe_pk;
                        $pickupHoursData[$oneRow->branches_id] = array($oneRow->weekday_pickup_start, $oneRow->weekday_pickup_end);
                    }
                }

                $returnHTML .= '
                
                <script>
                    let stripe_pkData = '.json_encode($stripe_pkData).';
                    let pickupHoursData = '.json_encode($pickupHoursData).';
                    		
                    //setTimeout(function() {
                        // document.getElementById("name").focus();
                        //loadDateFunction();
                    //}, 500);

                </script>
                <script src="/website_assets/js/checkout.js"></script>
                

            </div>
        </section>';
        $returnHTML .= $this->footerHTML();

		return $returnHTML;
	}


    public function confirmCheckOut(){
        
        $POST = json_decode(file_get_contents('php://input'), true);
        $this->db->writeIntoLog("confirmCheckOut=>POST:".json_encode($POST));
        
        // var_dump($POST);exit;

        $message = 'Sorry, could not save shipping address.';
        $savemsg = 'error';
        
        $pos_id = $paymentIntentId = 0;        
        $branches_id = $_SESSION["branches_id"]??0;        
        $customers_id = $_SESSION["customers_id"]??0;

        $cartsData = array_key_exists('cartsData', $POST) ? $POST['cartsData'] : array();
        $grandTotalPrice = array_key_exists('grandTotalPrice', $POST) ? $POST['grandTotalPrice'] :0;
        $subTotalPrice = array_key_exists('subTotalPrice', $POST) ? $POST['subTotalPrice'] :0;
        // var_dump($grandTotalPrice);exit;
        
        $paymentIntentId = trim(stripslashes((string) array_key_exists('paymentIntentId', $POST) ? $POST['paymentIntentId']:''));
        $paymentMethodId = trim(stripslashes((string) array_key_exists('paymentMethodId', $POST) ? $POST['paymentMethodId']:''));
        
        $subTotalPrice = floatval($POST['subTotalPrice']??0.00);
        $service_fee = floatval($POST['service_fee']??1.99);
        $tax1 = floatval($POST['tax1']??0.00);
        $expct_date = $POST['expct_date']??'0000-00-00 00:00:00.000';

        if($customers_id == 0){
            $message = 'Missing customer info.';
            $error = 'Error';
        }
        elseif(empty($cartsData)){
            $message = 'You have to add at least one Product.';
            $error = 'Error';
        }

        // var_dump(date('Y-m-d H:i:s', strtotime($expct_date)));exit;
        
        if(empty($error)){

            // var_dump('error empty');exit;

            $invoice_no = 1;
            $poObj = $this->db->getData("SELECT invoice_no FROM pos ORDER BY invoice_no DESC LIMIT 0, 1", array());
            if($poObj){
                $invoice_no = $poObj[0]['invoice_no']+1;
            }
            
            $posData = array('users_id' => 0,
                            'invoice_no' => $invoice_no,
                            'sales_datetime' => date('Y-m-d H:i:s'),
                            'customers_id' => $customers_id,
                            'service_fee' => 1.99,
                            'taxes_name1' => 'HST',
                            'taxes_percentage1' => 13.00,
                            'tax_inclusive1' => 0,

                            'order_status' => 0,

                            'pos_publish' => 1,
                            'created_on' => date('Y-m-d H:i:s'),
                            'last_updated' => date('Y-m-d H:i:s'),
                            'is_due' => 1,
                            'branches_id' => $branches_id,
                            'paymentIntentId' => $paymentIntentId,
                            'paymentMethodId' => $paymentMethodId,                            
                            'service_datetime' => date('Y-m-d H:i:s', strtotime($expct_date)),
                            // 'pos_type' => 'Sale',
                            // 'employee_id' => 0,
                            // 'pickup_minutes'=>15
                        );


            $pos_id = $this->db->insert('pos', $posData);

            // var_dump($pos_id);exit;

            if($pos_id>0){

                $amount_due = 0;

                foreach($cartsData as $product_id=>$cartInfo){

                    $productInfo = $cartInfo[0];
                    $productCMInfo = $cartInfo[1];

                    if(!empty($productInfo)){
                        $description = trim(stripslashes((string) $productInfo['name']));
                        $sku = trim(stripslashes((string) $productInfo['sku']));
                        $qty = intval($productInfo['qty']);
                        $newsales_price = floatval($productInfo['newsales_price']);
                        $regular_price = floatval($productInfo['regular_price']);
                        $product_prices_id = intval($productInfo['product_prices_id']);
                        $choice_more = count($productCMInfo);
                        $ave_cost = 0;
                        $sales_price = $newsales_price;
                        $insertdata = array('pos_id'=>$pos_id,
                                            'item_id'=>$product_id,
                                            // 'item_type'=>'product',
                                            'description'=>$description,
                                            // 'choice_more'=>$choice_more,
                                            // 'add_description'=>'',
                                            'sales_price'=>$regular_price,
                                            'qty'=>$qty,
                                            'shipping_qty'=>0,
                                            // 'return_qty'=>0,
                                            // 'ave_cost'=>$ave_cost,
                                            // 'discount_is_percent'=>1,
                                            // 'discount'=>0.00,
                                            // 'taxable'=>1
                                        );
                        $pos_cart_id = $this->db->insert('pos_cart', $insertdata);

                        if($pos_cart_id){

                            if($sales_price != $newsales_price){
                                $description .= " [$newsales_price]";
                                $this->db->update('pos_cart', array('description'=>$description, 'sales_price'=>$sales_price), $pos_cart_id);
                            }
                            $amount_due += round($regular_price * $qty, 2);

                        }
                    }
                    
                    // var_dump($amount_due);exit;

                    if(!empty($contact_no) || !empty($email)){
                        //if(!empty($contact_no))
                            //$this->sendSMSByPosId($pos_id);

                        //if(!empty($email))
                            //$this->jquery_sendposmail($pos_id, $email, $amount_due, 0);
                    }
                }

                $payment_amount = round($amount_due + $service_fee + $tax1,2);
                $ppData = array('pos_id' => $pos_id,
                            'payment_method' => 'Stripe',
                            'payment_amount' => round($payment_amount,2),	
                            'payment_datetime' => date('Y-m-d H:i:s'),
                            'drawer' => ''
                            );
                $this->db->insert('pos_payment', $ppData);

                // var_dump('Ok');exit;

                $savemsg = 'Added';
                $message = 'Your Order has been submit successfully. Within shortly, one of our order management will contact with you.';
                
                if(isset($_SESSION["clientSecret"])){
                    unset($_SESSION["clientSecret"]);
                }
                if(isset($_SESSION["paymentIntentId"])){
                    unset($_SESSION["paymentIntentId"]);
                }
                if(isset($_SESSION["price"])){
                    unset($_SESSION["price"]);
                }
                if(isset($_SESSION["branches_id"])){
                    unset($_SESSION["branches_id"]);
                } 

                //############################################################################################################

                $name = $email = $contact_no = '';
                if($customers_id>0){
                    $customersObj = $this->db->getObj("SELECT name, email, phone, branches_id FROM customers WHERE customers_id = $customers_id", array());
                    if($customersObj){
                        while($oneRow = $customersObj->fetch(PDO::FETCH_OBJ)){
                            $name = $oneRow->name;
                            $email = $oneRow->email;
                            $contact_no = $oneRow->contact_no;
                            if($branches_id==0){
                                $branches_id = $oneRow->branches_id;
                                $_SESSION["branches_id"] = $branches_id;
                            }
                        }
                    }
                }

                $branchesName = '';
                $queryObj = $this->db->getObj("SELECT name FROM branches WHERE branches_id = $branches_id", array());
                if($queryObj){
                    $branchesName = stripslashes(trim($queryObj->fetch(PDO::FETCH_OBJ)->name));
                }

                $POSTFIELDS = array();
                $POSTFIELDS['to'] = '/topics/admin-'.$branches_id;
                $POSTFIELDS['notification'] = array('body'=>"New Order # $invoice_no submitted from $name, $email, $contact_no at $branchesName, Please Accept/Cancel this Order.",
                                                'priority'=>'high',
                                                'subtitle'=>'Paradise Shawarma',
                                                'title'=>"New Order # $invoice_no submitted",
                                                );
                $POSTFIELDS['data'] = array('customerName'=>"$name, $email, $contact_no",
                                            'pos_id'=>$pos_id,
                                            'branches_id'=>$branches_id
                                            );
        
                $POSTFIELDSData = json_encode($POSTFIELDS);
                
                
                /**
                 * Messaging Notification Services
                 */
                //==========For Customer=============//
                // $headers = array( 'Authorization: key=AAAArlwdB-4:APA91bFG4WoYzCYYFqxt1mdVWiZEpS_Lx0DpXkLjmvGkywpwQewQQ366gUwd_p9SWmK9E-MnqVypyO7MleINCuf161NQ7HHHoWtq-Ekp6gdsot_PH81LxSvgqEDzKTrtLY8ql7L0PQJq',
                //                     'Content-Type: application/json');
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                // curl_setopt($ch, CURLOPT_POST, true);
                // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDSData);

                // $response = curl_exec($ch);
                // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // curl_close($ch);

                //=====================For Super Admin======================//
                // $POSTFIELDS['to'] = '/topics/admin-0';
        
                // $POSTFIELDSData = json_encode($POSTFIELDS);
                
                // $headers = array( 'Authorization: key=AAAArlwdB-4:APA91bFG4WoYzCYYFqxt1mdVWiZEpS_Lx0DpXkLjmvGkywpwQewQQ366gUwd_p9SWmK9E-MnqVypyO7MleINCuf161NQ7HHHoWtq-Ekp6gdsot_PH81LxSvgqEDzKTrtLY8ql7L0PQJq',
                //                     'Content-Type: application/json');
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                // curl_setopt($ch, CURLOPT_POST, true);
                // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDSData);

                // $response = curl_exec($ch);
                // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // curl_close($ch);

                // $this->db->writeIntoLog("httpCode: $httpCode, response:".json_encode($response));
                // $this->db->writeIntoLog("httpCode: $httpCode, response:".json_encode($response));
                /*####################* End Messaging Notification Services *######################*/


                /**
                 * Email Notification Services
                 */                
                $emailck = $email;
                $returnStr = '';
                
                if($emailck =='' || is_null($emailck)){
                    $returnStr = 'Could not send mail because of missing your email address.';
                }
                else{

                    //================ Test message ===================
                    // $msg = "First line of text\nSecond line of text";
                    // use wordwrap() if lines are longer than 70 characters
                    // $msg = wordwrap($msg,70);
                    // send email
                    // mail("imranmailbd@gmail.com","My subject",$msg);
                    ///===============================================

                    //======================For Customer====================//

                    //####################################
                    $fromName = trim(stripslashes($name));
                    $do_not_reply = $this->db->supportEmail('do_not_reply');
                    $email = $email;                 
                    $from = 'imran@sksoftsolutions.ca';   // $this->db->supportEmail('info');  //  "imran@sksoftsolutions.ca";  // //'info@sksoftsolutions.ca'; 
                    $subject = 'Service order place on '.LIVE_DOMAIN.' successfully'; 

                    // Set content-type header for sending HTML email 
                    $headers = "MIME-Version: 1.0" . "\r\n"; 
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";                      
                    // $headers .= COMPANYNAME." <$do_not_reply>". "\r\n";   //X-Sender                   
                    // $headers .= "PHP/".phpversion() . "\r\n";   //X-Mailer                   
                    // $headers .= '1'. "\r\n";   //X-Priority                  
                    // $headers .= "text/html\r\n". "\r\n";   //Content-type                  
                    // $headers .= "Reply-To: ".$do_not_reply. "\r\n";   //Reply-To                  
                    // $headers .= "Organization: ".COMPANYNAME. "\r\n";   //Organization                  
                    // Additional headers 
                    $headers .= 'From: '.COMPANYNAME.'<'.$from.'>' . "\r\n"; 
                    // $headers .= 'Cc: imran@sksoftsolutions.ca' . "\r\n"; 
                    // $headers .= 'Bcc: imran@sksoftsolutions.ca' . "\r\n";                      
                    //#####################   
                     
                    $message = ' 
                    <html> 
                    <head> 
                        <title>Welcome to '.COMPANYNAME.'</title> 
                    </head> 
                    <body> 
                        <h1>Dear <i><strong>'.$fromName.'</strong></i>,<br />Thanks you for place your service order to '.COMPANYNAME.'! We received your request. Our agent will contact with you asap.<br /><br /></h1> 
                        <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;">                             
                            <tr> 
                                <th>Your Name:</th><td>'.$fromName.'</td> 
                            </tr> 
                            <tr style="background-color: #e0e0e0;"> 
                                <th>Email:</th><td>'.$email.'</td> 
                            </tr> 
                            <tr style="background-color: #e0e0e0;"> 
                                <th>Your Tracking/Order No:</th><td>'.$invoice_no.'</td> 
                            </tr>                          
                            <tr style="background-color: #e0e0e0;"> 
                                <th>Thank you for the service request.</th><td>We will reply as soon as possible.</td> 
                            </tr>
                        </table> 
                    </body> 
                    </html>'; 

                    // var_dump($message);
                    // echo "<br>";
                    // var_dump($headers);
                    // echo "<br>";
                    // var_dump($email);exit;                    


                    if(mail($email, $subject, $message, $headers)){
                        
                        //=====================For Super Admin======================//
                        // Set content-type header for sending HTML email 
                        $fromName = COMPANYNAME; 
                        $do_not_reply = $this->db->supportEmail('do_not_reply'); 
                        $to = $this->db->supportEmail('info');   // 'imran.skitsbd@gmail.com';   //'user@example.com'; 
                        $cname = trim(stripslashes($name));  
                        $email = $email;
                        $subject = 'New Order # '.$invoice_no.' submitted'; 

                        $headersAdmin = array();
                        // $headersAdmin = "Organization: ".COMPANYNAME. "\r\n"; 
                        $headersAdmin = "MIME-Version: 1.0" . "\r\n"; 
                        $headersAdmin .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
                        // $headersAdmin .= "X-Priority: 3" . "\r\n"; 
                        // $headersAdmin .= "X-Mailer: PHP".phpversion() . "\r\n";                         
                        // Additional headers 
                        $headersAdmin .= 'From: '.$cname.'<'.$email.'>' . "\r\n"; 
                        // $headersAdmin .= 'Cc: imran.skitsbd@gmail.com' . "\r\n"; 
                        // $headersAdmin .= 'Bcc: imran@sksoftsolutions.ca' . "\r\n";  
                        
                        $messageAdmin = ' 
                        <html> 
                        <head> 
                            <title>'.$subject.'</title> 
                        </head> 
                        <body> 
                            <h1>Dear Admin of <i><strong>'.COMPANYNAME.'</strong></i>,<br /></h1>
                            New Order submitted, Please Accept/Cancel this Order.<br /><br /> 
                            <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
                                <tr> 
                                    <th>Order #</th><td>'.$invoice_no.'</td> 
                                </tr> 
                                <tr> 
                                    <th>Customer Name:</th><td>'.$name.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Customer Email:</th><td>'.$email.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Branch:</th><td>'.$branchesName.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Please take action him/her as soon as possible.</th><td>&nbsp;</td> 
                                </tr>
                            </table> 
                        </body> 
                        </html>'; 
                                     
                        // var_dump($messageAdmin);
                        // echo "<br>";
                        // var_dump($headersAdmin);
                        // echo "<br>";
                        // var_dump($to);exit;
                        
                        mail($to, $subject, $messageAdmin, $headersAdmin);
                        //==============================================================
                        
                    }
                    else{
                        $returnStr = "Sorry! Could not send mail. Try again later.";
                    }
                }

                
                

                /*###################* End Email Notification Services *#######################*/

            }
        }
        
        //$newCustomerData = $newPOSData
        return json_encode(array('login'=>'', 'savemsg'=>$savemsg, 'pos_id'=>$pos_id, 'message'=>$message));

    }



    public function getproduct_prices($product_id, $regular_price, $sales_price, $oldqty=1, $qty=1, $retArr = 0, $typeMatch = 0){
		$returnval = false;
		if($product_id>0){
			$todaydate = date('Y-m-d');
			$product_prices_id = $minQty = 0;
			
			$strextra = "SELECT * FROM product_prices WHERE product_id = $product_id AND ((start_date <= '$todaydate' AND end_date >= '$todaydate') OR start_date IN ('0000-00-00', '1000-01-01'))";
			if($typeMatch>0){
                $salesPrice = $sales_price*$qty;
				$strextra .= " AND ((price_type = 'Sale' AND type_match <= $salesPrice) OR (price_type = 'Quantity' AND type_match <= $qty))";
			}
			
			$lowestprice = $regular_price;
			$discountStr = '';
			$query = $this->db->getObj($strextra, array());
			if($query){
				while($oneRow = $query->fetch(PDO::FETCH_OBJ)){
					$price_type = $oneRow->price_type;
					$type_match = $oneRow->type_match;
					$is_percent = $oneRow->is_percent;
					$price = $oneRow->price;
					
					if($price_type == 'Quantity' && $oldqty===$qty && $sales_price !=$regular_price){
						$lowestprice = $sales_price;
						$product_prices_id = $oneRow->product_prices_id;
						$minQty = $type_match;
					}
					else{
						if($is_percent>0 && $price>0){
							$discountprice = round($regular_price*$price*0.01,2);
							$newsaleprice = $regular_price-$discountprice;
							$discountStr = "<small class=\"onsale\">-$price%</small>";
						}
						else{
							$newsaleprice = $price;
							$discountStr = "<small class=\"onsale\">Flat: -".($regular_price-$price)."</small>";
						}
						if($lowestprice==0 || $lowestprice==false){
							$lowestprice = $newsaleprice;
							$product_prices_id = $oneRow->product_prices_id;
							$minQty = $type_match;
						}
						elseif($lowestprice>$newsaleprice){
							$lowestprice = $newsaleprice;
							$product_prices_id = $oneRow->product_prices_id;
							$minQty = $type_match;
						}						
					}
				}
			}
            
            $weekDay = date('w');
            $todays_dealObj = $this->db->getObj("SELECT is_percent, price FROM todays_deal WHERE product_id = $product_id AND deal_days = $weekDay ORDER BY price ASC LIMIT 0,1", array());
            if($todays_dealObj){
                $todays_dealOneRow = $todays_dealObj->fetch(PDO::FETCH_OBJ);
                $is_percent = intval($todays_dealOneRow->is_percent);
                $price = $todays_dealOneRow->price;
                if($is_percent>0 && $price>0){
                    $discountprice = round($regular_price*$price*0.01,2);
                    $newsales_price = $regular_price-$discountprice;
                    $discountStr = "$price%";
                }
                else{
                    $newsales_price = $price;
                    $discountStr = "Flat: -".($regular_price-$price)."";
                }
            }
            
			if($retArr == 0){
				$returnval = $lowestprice;
			}
			else{
				$returnval = array($lowestprice, $discountStr, $product_prices_id, $minQty);
			}
			//$returnval = $strextra;//
		}
		return $returnval;
	}

	public function whyChooseUs(){  
        $returnHTML = $this->headerHTML();
        $returnHTML .= '
        <section class="blog-area" style="padding:20px 0; background-color:#E5F8F1; z-index: -1;">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 order-md-1">';
                        
                        $returnHTML .= $this->sidebarHTML();   

                    $returnHTML .= '</div>
                    <div class="col-md-8">
                        <div class="why-choose-us-area" style="padding:20px 0;">'; 

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
                    </div>
                </div>
            </div>
        </section>';

        $returnHTML .= $this->footerHTML();
		return $returnHTML;
	}

    public function newsMain(){ 
        
        $returnHTML = $this->headerHTML();

        $returnHTML .= '
        <section class="blog-area" style="padding:20px 0; background-color:#E5F8F1; z-index: -1;">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 order-md-1">';
                        
                        $returnHTML .= $this->sidebarHTML();   

                    $returnHTML .= '</div>
                    <div class="col-md-8">'; 

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
                                        $catImgSrc =baseURL.str_replace("./", '/', $onePicture);
                                    }
                                }                                                               
                                    
                                $short_description_set = implode(' ', array_slice(str_word_count($short_description,1), 0, 25));
                                $returnHTML .= "<div class=\"flex-container-new\">
                                    <img class=\"card-img\" src=\"$catImgSrc\" alt=\"pic1\">
                                        <div class=\"card-text\">
                                            <h3>$name</h3>
                                            <p>By <span class=\"badge bg-secondary\" style=\"color:#000000\">$created_by</span> - $news_articles_date</p>
                                            
                                            <p>$short_description_set.... <span id=\"more\"> <a href=\"$uri_value.html\">more</a></span></p>  
                                        </div> 
                                </div>";                               
                                
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
                            $catImgSrc =baseURL.str_replace("./", '/', $onePicture);
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
                                    </div>";


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

    public function newses(){
        
        $id = $GLOBALS['id'];

        if($id>0){
            $returnHTML = $this->headerHTML();

            $returnHTML .= '<section style="padding:1px 0; background-color:#E5F8F1; z-index: -1;">
                <div class="container">                    
                    <div class="single-card card-style-one pl-2 mt-4 mb-3" style="padding-top:0px !important;">
                        <div class="card-content">
                            <div class="row">
                                <div class="col-md-4 order-md-1">';
                            
                                    $returnHTML .= $this->sidebarHTML();

                                $returnHTML .= '
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-12 details">';

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
                                                            $serviceImgUrl =baseURL.str_replace('./', '/', $onePicture);
                                                        }
                                                    }
                                                    if(!empty($serviceImgUrl)){
                                                        $serviceImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$serviceImgUrl\">";
                                                    }
                                                    else{
                                                        $serviceImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\">";
                                                    } 

                                                    $returnHTML .= "
                                                     $serviceImg
                                                     <h2 class=\"mb-10 fontdescription_two\">".trim(addslashes($onePageRow->name))."</h2>
                                                     <p class=\"pt-15\">
                                                        ".nl2br(trim(stripslashes($onePageRow->short_description)))."
                                                        <br><br>
                                                        ".nl2br(trim(stripslashes($onePageRow->description)))."
                                                    </p>";
                                                }
                                            }
                                            $returnHTML .= '
                                        </div>
                                    </div>
                                </div>
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

            $returnHTML .= '<section style="padding:1px 0; background-color:#E5F8F1; z-index: -1;">
                <div class="container">                    
                    <div class="single-card card-style-one pl-2 mt-4 mb-3" style="padding-top:0px !important;">
                        <div class="card-content">
                            <div class="row">
                                <div class="col-md-4 order-md-1">';
                            
                                    $returnHTML .= $this->sidebarHTML();

                                $returnHTML .= '
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-12 details">';

                                            $tablePageObj = $this->db->getObj("SELECT * FROM pages WHERE pages_id = $id", array());
                                            if($tablePageObj){                                    
                                                while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){
                                                    
                                                    $pages_id = $onePageRow->pages_id;
                                                    $name = trim(stripslashes($onePageRow->name));
                                                    $page_create_date = date('m/d/Y', strtotime($onePageRow->created_on));
                                                    $uri_value = trim(stripslashes($onePageRow->uri_value));

                                                    $pageImgUrl = ''; 
                                                    $filePath = "./assets/accounts/page_$pages_id".'_';
                                                    $pics = glob($filePath."*.jpg");
                                                    if(!$pics){
                                                        $pics = glob($filePath."*.png");
                                                    }
                                                    if($pics){
                                                        foreach($pics as $onePicture){
                                                            $pageImgUrl =baseURL.str_replace('./', '/', $onePicture);
                                                        }
                                                    }
                                                    if(!empty($pageImgUrl)){
                                                        $pageImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$pageImgUrl\">";
                                                    }
                                                    else{
                                                        $pageImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\" >";
                                                    } 

                                                    $returnHTML .= "
                                                    $pageImg
                                                    <h2 class=\"mb-10 fontdescription_two\">".trim(addslashes($onePageRow->name))."</h2>
                                                    <p class=\"pt-15\">
                                                        ".nl2br(trim(stripslashes($onePageRow->short_description)))."
                                                    </p>
                                                    <p class=\"pt-15\">
                                                        ".nl2br(trim(stripslashes($onePageRow->description)))."
                                                    </p>";
                                                }
                                            }
                                            $returnHTML .= '
                                        </div>
                                    </div>
                                </div>
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

    public function aboutPesterminate(){        
        
        $id = 3;

        $bodyPages = array(1=>array(), 2=>array(), 3=>array(), 4=>array(), 5=>array(), 6=>array(), 7=>array(), 8=>array(), 9=>array(), 10=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, name, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($bodyPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $bodyPages[$oneRow->pages_id] = array(trim(stripslashes($oneRow->name)), trim(stripslashes($oneRow->short_description)));
            }
        }            
        $returnHTML = $this->headerHTML();

        $returnHTML .= '            
        <section style="padding:1px 0; background-color:#E5F8F1; z-index: -1;">
            <div class="container">                    
                <div class="single-card card-style-one pl-2 mt-4 mb-3" style="padding-top:0px !important;">';
                
                    $tablePageObj = $this->db->getObj("SELECT * FROM pages WHERE pages_id = $id", array());
                    if($tablePageObj){
                    
                        while($onePageRow = $tablePageObj->fetch(PDO::FETCH_OBJ)){

                            $pages_id = $onePageRow->pages_id;
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
                                    $pageImgUrl =baseURL.str_replace('./', '/', $onePicture);
                                }
                            }
                            if(!empty($pageImgUrl)){
                                $pageImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$pageImgUrl\">";
                            }
                            else{
                                $pageImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\" >";
                            } 

                        
                            $returnHTML .= "
                            <div class=\"card-content\">
                                <div class=\"row\">
                                    <div class=\"col-md-4 order-md-1\">";
                                        
                                        $returnHTML .= $this->sidebarHTML();   

                                        $returnHTML .= "
                                    </div>
                                    <div class=\"col-md-8\">
                                        <div class=\"row\">
                                            <div class=\"col-md-12 details\">
                                                $pageImg
                                                <h2 class=\"mb-10 fontdescription_two\">$name</h2>
                                                <p class=\"pt-15\">
                                                    ".nl2br(trim(stripslashes($onePageRow->short_description)))."
                                                </p>
                                                <ul class=\"list\">";
                                                    $metaUrl = $this->db->seoInfo('metaUrl');
                                                    foreach($metaUrl as $oneMetaUrl=>$label){
                                                        $returnHTML .= "<li><a style=\"font-family:Roboto !important; font-style: normal; font-size:20px; line-height:30px; font-weight: 300; font-display: swap;\" title=\"$label\" href=\"/$oneMetaUrl\">$label</a></li>";
                                                    }                    
                                                    $returnHTML .= "
                                                </ul>
                                            </div>
                                            <div class=\"col-md-12 details mb-15\">
                                                ".nl2br(trim(stripslashes($onePageRow->description)))."
                                            </div>
                                        </div>
                                        <div class=\"row mt-15\">
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
                            </div>";
                        }
                    }
                $returnHTML .= '
                </div>
            </div>
        </section>';
        $returnHTML .= $this->footerHTML();
        
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
                                                                                                    $videoImgUrl =baseURL.str_replace('./', '/', $onePicture);
                                                                                                }
                                                                                            }
                                                        
                                                                                                        
                                                                                            // var_dump($services_uri);exit;                                                 
                                                                                                    
                                                        
                                                                                            $returnHTML .='                                                                                                                                                                
                                                                                            <div class="col-md-4 text-center" style="border:0px solid red; margin:0 auto;">
                                                                                                <div class="video-box" style="height: 300px; background-image: url('.$videoImgUrl.'); background-repeat: no-repeat; background-position: center; background-size: cover;">
                                                                                                    <div class="video-btn">
                                                                                                        <a target="_blank" href="'.$video.'" class="show-effect"><span class="fa-sharp fa-solid fa-play"></span></a>
                                                                                                    </div>
                                                                                                </div> 
                                                                                                <span style="font-family:Rubik !important; font-style: normal; font-display: swap;" class="mb-10 mt-50">'.$name.'</span>                                                                       
                                                                                            </div>';
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
        
        $returnHTML .='
        <section style="padding:1px 0; background-color:#E5F8F1; z-index: -1;">
            <div class="container">
                <div class="single-card card-style-one pl-2 mt-4 mb-3" style="padding-top:0px !important;">';

                        $returnHTML .= "
                        <div class=\"card-content\">
                            <div class=\"row\">";                                            

                                $returnHTML .= "<div class=\"col-md-4 order-md-1\">";
                                    
                                $returnHTML .= $this->sidebarHTML();   

                                $returnHTML .= "</div> "; 

                                $returnHTML .= "<div class=\"row col-md-8\" style=\"border:0px solid red !important;\">"; 
                                
                                                    $returnHTML .= '<!-- services Section -->
                                                    <section class="gallery-area section" style="padding-top:12px !important; background-image: -webkit-linear-gradient( 90deg, rgba(233, 247, 242, 0.4) 0%, rgb(233, 247, 242) 100% ) !important; padding: 2px 0 2px; width:100%; border:0px solid red !important; position:relative !important;">
                                                        <div class="container">

                                                            <div class="section-title text-center">                                            
                                                                <h2>Our Service Photo Gallery</h2>
                                                            </div>';
                                                            
                                                                $gallerySql = "SELECT photo_gallery_id, name FROM photo_gallery WHERE photo_gallery_publish = 1 LIMIT 0, 6";
                                                                $galleryObj = $this->db->getObj($gallerySql, array());
                                                                if($galleryObj){


                                                                    $picturesStr = ''; 

                                                                    $returnHTML .='
                                                                    <div class="pic-section-padding" id="gallery">
                                                                        <div class="container">
                                                                
                                                                        <div id="btncontainer" class="picture-filter">
                                                                            <a class="btn btn-active" href="#all">ALL</a>';

                                                                            while($oneGalleryRow = $galleryObj->fetch(PDO::FETCH_OBJ)){
                                                                                $photo_gallery_id = $oneGalleryRow->photo_gallery_id;
                                                                                $name = stripslashes(trim((string) $oneGalleryRow->name));
                                                                                
                                            
                                                                                $returnHTML .= '<a class="btn" href="#'.$name.'">'.$name.'</a>'; 

                                                                            
                                                                                $filePath = "./assets/accounts/photo_$photo_gallery_id".'_';
                                                                                $pics = glob($filePath."*.jpg");
                                                                                if(empty($pics) || !$pics){
                                                                                    $pics = glob($filePath."*.png");
                                                                                }                            
                                                                                if($pics){

                                                                                    foreach($pics as $onePicture){
                                                                                        $prodImg = str_replace("./assets/accounts/", '', $onePicture);
                                                                                        $photo_galleryImgUrl =baseURL.str_replace('./', '/', $onePicture);
                                                                                        
                                                                                        $picturesStr .= '<a class="all '.$name.'"><img id="picgall" alt="'.strip_tags(addslashes($name)).'" src="'.$photo_galleryImgUrl.'"/></a>';

                                                                                        // $picturesStr .= '<div class="col-lg-3 col-md-6 col-xs-12 col-sm-12 mix id_'.$photo_gallery_id.'">
                                                                                        //     <div class="gallery">
                                                                                        //         <figure><a href="'.$photo_galleryImgUrl.'">
                                                                                        //             <img alt="'.strip_tags(addslashes($name)).'" src="'.$photo_galleryImgUrl.'">
                                                                                        //         <span></span>
                                                                                        //     </a></figure>
                                                                                        //     </div>
                                                                                        // </div>';     
                                        
                                                                                    }
                                                                                }
                                                                            }

                                                                            $returnHTML .='
                                                                            
                                                                        </div>
                                                                
                                                                        <div class="picture-gallery picture-sets">
                                                                            
                                                                        '.$picturesStr.'
                                                                            
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                    '; 
                                                            }

                                                            
                                                            $returnHTML .='</div>
                                                    </section>
                    
                                                </div>
                            </div>
                        </div>';                    
                        $returnHTML .= '
                </div>
                    
            </div>
        </section>';
        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }
    

    public function servicesMain(){ 
        
        $returnHTML = $this->headerHTML();
        
        $returnHTML .='
        <section style="padding:1px 0; background-color:#E5F8F1; z-index: -1;">
            <div class="container">                    
                <div class="single-card card-style-one pl-2 mt-4 mb-3" style="padding-top:0px !important;">
                    <div class="card-content">
                        <div class="row">
                            <div class="col-md-4 order-md-1">';
                        
                                $returnHTML .= $this->sidebarHTML();

                                $returnHTML .= '
                            </div>
                            <div class="col-md-8">
                                <div class="col-md-12">
                                    <h2 class="mb-10 fontdescription_two">Common Pest Control</h2>
                                </div>
                                <div class="service-wrapper service-area mt-15">
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
                        </div>
                    </div>
                </div>
            </div>
        </section>';
        $returnHTML .= $this->footerHTML();
        
        
		return $returnHTML;
    }

    public function services(){
        
        $id = $GLOBALS['id'];

        if($id>0){

            $returnHTML = $this->headerHTML();

            $returnHTML .= '<section style="padding:1px 0; background-color:#E5F8F1; z-index: -1;">
                <div class="container">                    
                    <div class="single-card card-style-one pl-2 mt-4 mb-3" style="padding-top:0px !important;">
                        <div class="card-content">
                            <div class="row">
                                <div class="col-md-4 order-md-1">';
                            
                                    $returnHTML .= $this->sidebarHTML();

                                $returnHTML .= '
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-12 details">';
                    
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
                                                            $serviceImgUrl =baseURL.str_replace('./', '/', $onePicture);
                                                        }
                                                    }
                                                    if(!empty($serviceImgUrl)){
                                                        $serviceImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"$serviceImgUrl\">";
                                                    }
                                                    else{
                                                        $serviceImg = "<img class=\"detailsPagePicture\" alt=\"".strip_tags(addslashes($onePageRow->name))."\" src=\"/assets/admin/images/event/1.jpg\">";
                                                    } 

                                                    $returnHTML .= "
                                                    $serviceImg
                                                    <h2 class=\"mb-10 fontdescription_two\">".trim(addslashes($onePageRow->name))."</h2>
                                                    <p class=\"pt-15\">
                                                        ".nl2br(trim(stripslashes($onePageRow->short_description)))."
                                                        <br><br>
                                                        ".nl2br(trim(stripslashes($onePageRow->description)))."
                                                    </p>
                                                    ";                                
                                                }
                                            }

                                            $returnHTML .= '
                                        </div>
                                    </div>
                                </div>
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
    
    public function contactUs(){
        
        $contactUsPages = array(1=>array(), 2=>array(), 4=>array(), 8=>array(), 9=>array(), 10=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, short_description, description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($contactUsPages)).") AND pages_publish =1", array());
		if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $contactUsPages[$oneRow->pages_id] = array(nl2br(stripslashes(trim((string) $oneRow->description))), $oneRow->uri_value, nl2br(stripslashes(trim((string) $oneRow->short_description))));
            }
        }

        $returnHTML = $this->headerHTML();
        $returnHTML .= '
        <section class="contact-form-section" style="background-color:#E5F8F1; z-index: -1;">
            <div class="container">
                <div class="pageTransBody">
                    <div class="row">
                        <div class="col-md-4 order-md-1">';
                                
                            $returnHTML .= $this->sidebarHTML();   

                        $returnHTML .= '</div>
                        <div class="col-md-8">                            
                            <div class="row"> 
                                <div class="col-sm-6">
                                    <div class="card card-edit bg-color p-30">
                                        <h4><i class="fa fa-phone"></i> Phone</h4>';
                                        if(!empty($contactUsPages[1][2])){
                                            $returnHTML .= $contactUsPages[1][2];
                                        }
                                        $returnHTML .= '<br>
                                        <br>
                                        <h4><i class="fa fa-envelope"></i> E-mail Us</h4>';
                                        if(!empty($contactUsPages[2][2])){
                                            $returnHTML .= $contactUsPages[2][2];
                                        }
                                        $returnHTML .= '<br><br>';
                                        $addressBoxStr = '';
                                        $tableObj = $this->db->getObj("SELECT name, address, google_map, working_hours FROM branches WHERE branches_publish =1", array());
                                        if($tableObj){
                                            $l=0;                                            
                                            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                                                $l++;
                                                $returnHTML .= "<h4><i class=\"fa fa-map-marker\"></i> ".trim(stripslashes($oneRow->name))."</h4>
                                                ". nl2br(trim(stripslashes($oneRow->address))).'
                                                <br><br>';

                                                $addressBoxStr .= '<div class="col-sm-6">
                                                    <div class="card card-edit bg-color p-30">
                                                        <div class="title-part">
                                                            <h4>'.trim(stripslashes($oneRow->name)).'</h4>
                                                        </div>                   
                                                        <div class="contact-map">
                                                            <div class="gmap_canvas">                            
                                                                '.trim(stripslashes($oneRow->google_map)).'
                                                            </div>
                                                        </div> 
                                                    </div>
                                                </div>';
                                            }
                                        }
                                        $returnHTML .= '
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card pd-height bg-color p-30">
                                        <div class="title-part">
                                            <h4>Contact us Via Mail</h4>
                                        </div>
                                        <div class="contact-form form-style-one">
                                            <form action="#" id="contactUsForm" onsubmit="sendContactUs(event)">
                                                <div class="form-input mt-15">
                                                    <div class="input-items default">
                                                        <input required type="text" name="fname" placeholder="First Name *">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div>
                                                <div class="form-input mt-15">
                                                    <div class="input-items default">
                                                        <input type="text" name="lname" placeholder="Last Name *">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                </div> 
                                                <div class="form-input mt-15">
                                                    <div class="input-items default">
                                                        <input required type="number" name="phone" placeholder="Phone *"  maxlength="10">
                                                        <i class="fa-solid fa-phone"></i>
                                                    </div>
                                                </div>    
                                                <div class="form-input mt-15">
                                                    <div class="input-items default">
                                                        <input type="text" name="email" placeholder="Email *">
                                                        <i class="fa-solid fa-envelope text-normal"></i>
                                                    </div>
                                                </div> 
                                                <div class="form-input mt-15">
                                                    <div class="input-items default">
                                                        <textarea style="padding-right:102px !important;" name="note" placeholder="Massage"></textarea>
                                                        <i class="fas fa-massage fa-pen-to-square"></i>
                                                    </div>
                                                </div> 
                                                <div class="form-group col-md-10" style="margin-top:10px; padding-left:0px;">
                                                    <div id="mathCaptcha"></div>
                                                    <span id="errRecaptcha" style="color:red"></span>
                                                </div>
                                                <div class="form-input standard-buttons mb-0 pb-0">
                                                    <span id="msgContact"></span>
                                                    <button name="submit-form" type="submit" class="main-btn-contact standard-two"><span>Submit</span></button>
                                                </div>
                                            </form>
                                            <!--Contact Form-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-15">
                                '.$addressBoxStr.'
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
/*
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
            // var_dump($message);
            // echo "<br>";
            // var_dump($headers);
            // echo "<br>";
            // var_dump($email);exit;

			if(mail($email, $subject, $message, implode("\r\n", $headers))){
                var_dump('tosend');exit;

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
*/

    function sendContactUs(){
        //================Email Options Here==============//
        
        $returnData = array();
        $returnData['savemsg'] = 'error';
        // $services_id = intval($_POST['services_id']??0);
		$first_name = addslashes(trim($_POST['fname']??''));
		$last_name = addslashes(trim($_POST['lname']??''));
		$contact_no = addslashes(trim($_POST['phone']??''));
		$email = addslashes(trim($_POST['email']??''));	
		$message = addslashes(trim($_POST['note']??''));	

        // var_dump($_POST);exit;
        
        $customerData = array();
		$customerData['name'] = $first_name." ".$last_name;
		// $customerData['last_name'] = $last_name;
		$customerData['phone'] = $contact_no;
		$customerData['email'] = $email;
		$customerData['note'] = $message;
		// $customerData['services_id'] = $services_id;
		
        $customers_id = 0;

        // $queryManuObj = $this->db->getObj("SELECT customers_id FROM customers WHERE phone = :phone", array('phone'=>$contact_no));
        // if($queryManuObj){
        //     $customers_id = $queryManuObj->fetch(PDO::FETCH_OBJ)->customers_id;						
        // } 

        // if($customers_id==0){
        //     $customerData['offers_email'] = 1;
        //     $customerData['customers_publish'] = 1;
        //     // $customerData['accounts_id'] = 0;
        //     $customerData['user_id'] = 0;
        //     $customerData['last_updated'] = date('Y-m-d H:i:s');
        //     $customerData['created_on'] = date('Y-m-d H:i:s');
        //     // var_dump($customerData);exit;
        //     $customers_id = $this->db->insert('customers', $customerData);
        // }

        //if($customers_id>0){ 

                //################Contact Email#################
                $returnStr = '';
                $email = array_key_exists('email', $_POST)?$_POST['email']:'';
                
                if($email =='' || is_null($email)){
                    $returnStr = 'Could not send mail because of missing your email address.';
                }
                else{
                    
                    /*$fromName = trim(stripslashes((string) $_POST['first_name']??''))." ".trim(stripslashes((string) $_POST['last_name']??''));
                    $contact_no = nl2br(trim(stripslashes((string) $_POST['contact_no']??'')));
                    $note = nl2br(trim(stripslashes((string) $_POST['message']??'')));           
                    $subject = '[New message] From '.LIVE_DOMAIN." Contact Form";
                              
                    
                    $message = "<!DOCTYPE html><html lang=\"en\">";
                    $message .= "<head>";
                    $message .= "<title>$subject</title>";
                    $message .= "</head>";
                    $message .= "<body>";
                    $message .= "<p>";
                    $message .= "Dear <i><strong>$fromName</strong></i>,<br />";
                    $message .= "We received your request for contact.<br /><br />";
                    $message .= "You wrote:<br />";
                    $message .= "Phone: $contact_no<br>";
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

                    $headers = array();                    
                    $do_not_reply = $this->db->supportEmail('do_not_reply');
                    $info = $this->db->supportEmail('info');
                    $headers["From"] = "info@sksoftsolutions.ca"; //COMPANYNAME." <$do_not_reply>";
                    $headers["X-Sender"] = "info@sksoftsolutions.ca"; //COMPANYNAME." <$do_not_reply>";
                    $headers["X-Mailer"] = "PHP/".phpversion();
                    $headers["X-Priority"] = '1';
                    $headers["MIME-Version"] = "1.0";
                    $headers["Content-type"] = "text/html\r\n";

                    $headers[] = "Reply-To: ".$do_not_reply;
                    $headers[] = "Organization: ".COMPANYNAME;  */

                    /*$headers  = 'MIME-Version: 1.0' . "\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
                    $headers = "From: info@sksoftsolutions.ca\n"." ".COMPANYNAME;*/

                    //####################################
                    $fromName = trim(stripslashes((string) $_POST['fname']??''))." ".trim(stripslashes((string) $_POST['lname']??''));
                    $email = array_key_exists('email', $_POST)?$_POST['email']:'';
                    $note = nl2br(trim(stripslashes((string) $_POST['note']??''))); 
                    $to = $email; //'user@example.com';                     
                    $from = $this->db->supportEmail('info');  //  "imran@sksoftsolutions.ca";  // //'info@sksoftsolutions.ca'; 
                    // $fromName = COMPANYNAME;  //'SK SOFT SOLUTIONS Inc.'; 
                    $do_not_reply = $this->db->supportEmail('do_not_reply');                     
                    $subject = 'New message From '.LIVE_DOMAIN.' Contact Form'; 
                     
                    $message = ' 
                        <html> 
                        <head> 
                            <title>Welcome to '.COMPANYNAME.'</title> 
                        </head> 
                        <body> 
                            <h1>Dear <i><strong>'.$fromName.'</strong></i>,<br />Thanks you for joining with us! We received your request for contact.<br /><br /></h1> 
                            <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
                                <tr>
                                    <th>You wrote:</th><td><br /></td> 
                                </tr>
                                <tr> 
                                    <th>Name:</th><td>'.$fromName.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Email:</th><td>'.$email.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Phone:</th><td>'.$contact_no.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Message:</th><td>'.$note.'</td> 
                                </tr> 
                                <tr style="background-color: #e0e0e0;"> 
                                    <th>Thank you for contacting us.</th><td>We will reply as soon as possible.</td> 
                                </tr> 
                                <!--tr> 
                                    <th>Website:</th><td><a href="'.LIVE_DOMAIN.'">'.LIVE_DOMAIN.'</a></td> 
                                </tr--> 
                            </table> 
                        </body> 
                        </html>'; 
                     
                    // Set content-type header for sending HTML email 
                    $headers = "MIME-Version: 1.0" . "\r\n"; 
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
                     
                    // Additional headers 
                    $headers .= 'From: '.COMPANYNAME.'<'.$from.'>' . "\r\n"; 
                    // $headers .= 'Cc: imran@sksoftsolutions.ca' . "\r\n"; 
                    // $headers .= 'Bcc: imran@sksoftsolutions.ca' . "\r\n";                      
                    //#####################   

                    // var_dump($message);
                    // echo "<br>";
                    // var_dump($headers);
                    // echo "<br>";
                    // var_dump($email);exit;

                    // the message
                    // $msg = "First line of text\nSecond line of text";
                    // use wordwrap() if lines are longer than 70 characters
                    // $msg = wordwrap($msg,70);
                    // send email
                    // mail("imranmailbd@gmail.com","My subject",$msg);


                    if(mail($email, $subject, $message, $headers)){
                        
                        $returnStr = 'sent';

                        // // var_dump($returnStr);exit;

                        // $info = $this->db->supportEmail('info');
                        // $headersAdmin = array();
                        // $headersAdmin[] = "From: ".$email;
                        // $headersAdmin[] = "Reply-To: ".$do_not_reply;
                        // $headersAdmin[] = "Organization: ".COMPANYNAME;
                        // $headersAdmin[] = "MIME-Version: 1.0";
                        // $headersAdmin[] = "Content-type: text/html; charset=iso-8859-1";
                        // $headersAdmin[] = "X-Priority: 3";
                        // $headersAdmin[] = "X-Mailer: PHP".phpversion();                
                        
                        // $message = "<html>";
                        // $message .= "<head>";
                        // $message .= "<title>$subject</title>";
                        // $message .= "</head>";
                        // $message .= "<body>";
                        // $message .= "<p>";
                        // $message .= "Dear Admin of <i><strong>".COMPANYNAME."</strong></i>,<br />";
                        // $message .= "We received a Contact request from $fromName.<br /><br />";
                        // $message .= "He / She wrotes:<br />";
                        // $message .= "Phone: $contact_no<br>";
                        // $message .= "Email: $email<br>";
                        // $message .= "Message: $note";
                        // $message .= "</p>";
                        // $message .= "<p>";
                        // $message .= "<br />";
                        // $message .= "Please reply him/her as soon as possible.";
                        // $message .= "</p>";
                        // $message .= "</body>";
                        // $message .= "</html>";

                        //####################################
                        $fromName = COMPANYNAME; 
                        $cname = trim(stripslashes((string) $_POST['fname']??''))." ".trim(stripslashes((string) $_POST['lname']??''));
                        $email = array_key_exists('email', $_POST)?$_POST['email']:'';
                        $to = $this->db->supportEmail('info');  //   'imran.skitsbd@gmail.com';  //'user@example.com';                     
                        $from = $email; //$this->db->supportEmail('do_not_reply');  
                        // $fromName = COMPANYNAME;  //'SK SOFT SOLUTIONS Inc.'; 
                        $do_not_reply = $this->db->supportEmail('do_not_reply');                     
                        $subject = 'New message From '.LIVE_DOMAIN.' Contact Form'; 
                        
                        $messageAdmin = ' 
                            <html> 
                            <head> 
                                <title>'.$subject.'</title> 
                            </head> 
                            <body> 
                                <h1>Dear Admin of <i><strong>'.COMPANYNAME.'</strong></i>,<br />We received a Contact request from '.$cname.'.<br /><br /></h1> 
                                <table cellspacing="0" style="border: 2px dashed #FB4314; width: 100%;"> 
                                    <tr>
                                        <th>He / She wrote:</th><td><br /></td> 
                                    </tr>
                                    <tr> 
                                        <th>Name:</th><td>'.$cname.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Email:</th><td>'.$email.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Phone:</th><td>'.$contact_no.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Message:</th><td>'.$note.'</td> 
                                    </tr> 
                                    <tr style="background-color: #e0e0e0;"> 
                                        <th>Please reply him/her as soon as possible.</th><td>&nbsp;</td> 
                                    </tr>
                                </table> 
                            </body> 
                            </html>'; 
                      
                        // Set content-type header for sending HTML email 
                        $headersAdmin = "MIME-Version: 1.0" . "\r\n"; 
                        $headersAdmin .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
                        
                        // Additional headers 
                        $headersAdmin .= 'From: '.$cname.'<'.$email.'>' . "\r\n"; 
                        // $headersAdmin .= 'Cc: imran.skitsbd@gmail.com' . "\r\n"; 
                        // $headersAdmin .= 'Bcc: imran@sksoftsolutions.ca' . "\r\n";                      
                        //#####################   

                    //     var_dump($messageAdmin);
                    // echo "<br>";
                    // var_dump($headersAdmin);
                    // echo "<br>";
                    // var_dump($to);exit;
                        
                        mail($to, $subject, $messageAdmin, $headersAdmin);
                        
                    }
                    else{
                        $returnStr = "Sorry! Could not send mail. Try again later.";
                    }
                }
                // return json_encode(array('login'=>'', 'returnStr'=>$returnStr));
                //################Appointment Email End#################
		        $returnData['savemsg'] = 'sent';
            
        //}
        
        return json_encode($returnData);
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
        
        $pageURI = str_replace('.html', '', implode('/', $GLOBALS['segments']));
        $tableObj = $this->db->getObj("SELECT * FROM seo_info WHERE uri_value = :uri_value AND seo_info_publish = 1 LIMIT 0, 1", array('uri_value'=>$pageURI));
		if($tableObj){
			$tableRow = $tableObj->fetch(PDO::FETCH_OBJ);
            
            $seo_info_id = $tableRow->seo_info_id;
			$metaTitle = trim(stripslashes($tableRow->seo_title));
            $metaKeyword = trim(stripslashes($tableRow->seo_keywords));
            $metaDescription = trim(stripslashes($tableRow->description));
            $metaUrl = trim(stripslashes($tableRow->video_url));
            $metaVideo = trim(stripslashes($tableRow->video_url));

            $pageImgUrl = '';
            $filePath = "./assets/accounts/seo_$seo_info_id".'_';
            $pics = glob($filePath."*.jpg");
            if(!$pics){
                $pics = glob($filePath."*.png");
            }
            if($pics){
                foreach($pics as $onePicture){
                    $pageImgUrl = str_replace('./', '/', $onePicture);
                }
            }

            if(!empty($pageImgUrl)){
                $metaImage = baseURL.$pageImgUrl;
            }            
		}
        if(empty($pageImgUrl)){
            $metaImage = baseURL.'/website_assets/images/logo.png';
        }


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

            $canonical = baseURL.'/'.$pageURI.'.html';
            if(empty($pageURI)){$canonical = baseURL.'/'.$pageURI;}

            $htmlStr .= '
            <meta property="og:image" content="'.$metaImage.'"/>
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
            <link rel="canonical" href="'.$canonical.'" />
            <meta property="og:url" content="'.$canonical.'" />
            <meta property="og:image" content="'.$metaImage.'" />

            
            <!--meta name="google-site-verification" content="uyGnbnfTDNRSZaWiDoXT-9k0LfHzd16ee0HtdIzA5N8" /-->
            
            <meta name="google-site-verification" content="K_16NHZZTJqApsd5ATmWNj9pXKSenCNjEO8IlsFYnho" />
            
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
            
            
            <link rel="stylesheet" href="/assets/css/jquery-ui.css"> 

            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z4HGG6NL40"></script>
            <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag("js", new Date());

            gtag("config", "G-Z4HGG6NL40");
            </script>
     
                    
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
                                                                    $subMenuUri = str_replace('//', '/', '/'.trim(stripslashes($oneRow2->menu_uri))).'.html';
                                                                }
                                                                if(in_array($oneRow2->menu_uri, array('residential', 'commercial'))){
                                                                    $subMenuUri = "/services$subMenuUri";
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
                                                $manuStr .='<li class="'.$activeDefault.'">
                                                                <div class="cart-menu shopping">
                                                                    <a class="mycart" title="My Cart" style="font-family:Rubik !important; font-style: normal; font-weight: 300; font-display: swap;" href="/My_Order.html">My Cart</a>&nbsp;
                                                                    <i class="fa-solid cart-effect fa-bag-shopping text-light fs-4 me-2" style="font-size:32px !important"></i>
                                                                    <a title="Checkout Your Order" href="/Checkout.html" id="headerCart">
                                                                        <span id="headerCartQty">0</span>
                                                                    </a>
                                                                    <div class="shopping-item" id="shoppingItem">
                                                                        <div class="dropdown-cart-header">
                                                                            <span id="cartQuantity"></span>
                                                                        </div>
                                                                        <ul class="shopping-list" id="shoppingList"></ul>
                                                                        <div class="bottom">
                                                                            <div class="total">
                                                                                <span>Total: </span>
                                                                                <span class="total-amount" id="totalAmount">0</span>
                                                                            </div>
                                                                            <a id="CheckoutLink" href="/checkout.html" class="btn animate">Checkout</a>
                                                                        </div>                                    
                                                                    </div>
                                                                </div>
                                                            </li>';

                                            $htmlStr .= $manuStr.'</ul>
                                        </div>
                                    </nav>
                                </div>
                                <div class="link-btn">
                                    <a href="/pest-services.html" class="main-btn main-btn-one">
                                    <span>Service Request</span>
                                    <h6 style="font-size:12px; color:#00ff00;"><span>Place an order</span></h6>                                    
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

        $headerPages = array(1=>array(), 2=>array(), 4=>array());
        $tableObj = $this->db->getObj("SELECT pages_id, short_description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($headerPages)).") AND pages_publish =1", array());
        if($tableObj){
            while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                $headerPages[$oneRow->pages_id] = trim(stripslashes($oneRow->short_description));
            }
        }
        
        $htmlStr = "<div class=\"HaveAnyQuestion\">
            <h4>Have Any Question?</h4>
            <p>Contact us, our service man will give you support ASAP to move your live without pest problem.</p>
            <ul>
                <li>
                    <span>
                    <i aria-hidden=\"true\" class=\"fs24 fas fa-phone-alt\"></i></span>
                    <span><a class=\"fs24\" href=\"tel:$headerPages[1]\">".$headerPages[1]."</a></span>
                </li>
                <li>
                    <span>
                        <i aria-hidden=\"true\" class=\"fs24 far fa-envelope\"></i>
                    </span>
                    <span>
                        <a href=\"email:$headerPages[2]\" class=\"link\" >email :&#160;".$headerPages[2]."</a>
                    </span>
                </li>
            </ul>
        </div>";

		$htmlStr .= "
        <div class=\"nav animated bounceInDown bg-light mt-15\">
            <ul>";
            
            $contactUsPages = array(8=>array(), 9=>array(), 10=>array());
            $tableObj = $this->db->getObj("SELECT pages_id, description, uri_value FROM pages WHERE pages_id IN (".implode(', ', array_keys($contactUsPages)).") AND pages_publish =1", array());
            if($tableObj){
                while($oneRow = $tableObj->fetch(PDO::FETCH_OBJ)){
                    $contactUsPages[$oneRow->pages_id] = array(nl2br(stripslashes(trim((string) $oneRow->description))), $oneRow->uri_value);
                }
            }
            $contactAddr = explode(",",$contactUsPages[10][0]);

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

                        $manuStr .= "<li class=\"\">";
                        $mobileManuStr .= "<li class=\"dropdown$activeDefault\">";
        
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
                                $subMenuUri = str_replace('//', '/', '/'.trim(stripslashes($oneRow2->menu_uri))).'.html';
                            }
                            $activeDefault = '';
                            if($currentURI==$oneRow2->menu_uri){
                                $activeDefault = ' active';
                                $activeYN++;
                            }

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
                        $manuStr .= "<li class=\"$activeDefault\"><a href=\"$rootMenu_uri\">$rootName</a></li>";
                        $mobileManuStr .= "<li class=\"$activeDefault\"><a href=\"$rootMenu_uri\">$rootName</a></li>";
                    }
                }
            }

        $htmlStr .= $manuStr."</ul>                
        </div>";        

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
                            <div class="images-gellary picture-gallery" style="border:0px solid red; width:90%;">';

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
                                            $photo_galleryImgUrl =baseURL.str_replace('./', '/', $onePicture);
                                            
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
                                    <li><a href="/services/residential.html">Services</a></li>
                                    <li><a href="/why-choose-us.html ">How It Works</a></li>
                                    <li><a href="/news-articles.html">News & Articles</a></li>
                                    <li><a href="/contact-us.html">Contact Us</a></li>
                                    <li><a href="/sitemap.html">Sitemap</a></li>
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
        <script src="/assets/js/jquery-ui.min.js"></script>
        <!--script src="/assets/js/common.js"></script-->
                
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