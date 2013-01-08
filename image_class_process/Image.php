<?php
define("IMAGE_FLIP_HORIZONTAL",	1);
define("IMAGE_FLIP_VERTICAL",	2);
define("IMAGE_FLIP_BOTH",		3);

define("LEFT_SIDE",  1);
define("MID_SIDE",   2);
define("RIGHT_SIDE", 3);

define("IMAGE_STANDARD_WIDTH",  80);
define("IMAGE_STANDARD_HEIGHT", 30);


/**
 * A Wrapper for GD library in PHP. GD must be installed in your system for this to work.
 * Example: $img = new Image('wheel.png');
 * 			$img->flip(1)->resize(120, 0)->save('wheel.jpg');
 *
 *
 * Pep-Talk(Software Lab). Copyright © Nitin Soni. All Rights Reserved.
 * join us:https://www.facebook.com/peptalk.softwarelab
 */
class Image {
	private $file_name;
	private $info;
	public  $width;
	public  $height;
	public  $image;
	private $org_image;
	public	$font_ttf;
	public  $font_size;
	public	$text_angle;
	/** 
	 * Constructor - 
	 * Arguments : Image Filepath
	 */
	function Image($image_file) {
		if(!function_exists('imagecreatefrompng')) return; //GD not available
		if(!file_exists($image_file) or !is_readable($image_file)) return;
		
		$this->file_name = $image_file;
		$img = getimagesize($image_file);

		//Create the image depending on what kind of file it is.
		switch($img['mime']) {
			case 'image/png' : $image = imagecreatefrompng($image_file); break;
			case 'image/jpeg': $image = imagecreatefromjpeg($image_file); break;
			case 'image/gif' : 
				$old_id = imagecreatefromgif($image_file); 
				$image  = imagecreatetruecolor($img[0],$img[1]); 
				imagecopy($image,$old_id,0,0,0,0,$img[0],$img[1]); 
				break;
			default: break;
		}
		$this->info		= $img;
		$this->width	= imagesx($image);
		$this->height	= imagesy($image);
		$this->image	= $this->org_image = $image;
	}
	
	/**
	 * Rotates the image to any direction using the given angle.
	 * Arguments: $angle - The rotation angle, in degrees.
	 * Example: $img = new Image("file.png"); $img->rotate(180); $img->show(); // Turn the image upside down.
	 */
	function rotate($angle, $background=0) {
		if(!$this->image) return false;
		if(!$angle) return $this;
		
		$this->image = imagerotate($this->image, $angle, $background);
		return $this;
	}

	/**
	 * Marge two the image to any direction using the given angle.
	 * Arguments: $angle - The rotation angle, in degrees.
	 * Example: $img = new Image("file.png"); $img->rotate(180); $img->show(); // Turn the image upside down.
	 */
	 
	/**
	 *  PATTERN FOR MARGE ANY IMG1 to IMG2
	 *  ARG1($src_im) is the source image 
	 *  ARG2($dst_x) shows positioning of image2(src_img) on $image1(obj_img) [axis-x]
	 *  ARG3($dst_y) shows positioning of image2(src_img) on $image1(obj_img) [axis-y]
	 *  ARG4($no_of_char) shows image2 contain number of images. 
	 *		Example: image2(src_img) actual size is[600*100]px and have 3 cortoon image where 1 cartoon image size is [200*100]px 
	 *  ARG5($select_char) shows image2 contain number of images.
	 *		Example: image2(src_img) have 3 images where we select any image in 3 images.	
	 *  ARG6($pct) shows image2(src_img) alpha on image1(obj_img)
	 *	Example: 
	 *		1. $image = new Image('bucket-angry-sprit.png');
	 *		2. $bg_img= new Image('back-ground.png');
	 *		3. $image->imagecopymerge_alpha($bg_img, 20, 50, 4, 2, 100); #CORRECT (Spriting based image copy) 
	 *		4. $image->show();
	 *
	 */
	function imagecopymerge_alpha($src_im, $dst_x, $dst_y, $no_of_char, $select_char, $pct) { 
		$src_im_w = floatval($src_im->width / $no_of_char);
		$src_im_h = floatval($src_im->height);
		
		$cut = imagecreatetruecolor($src_im_w, $src_im_h);
		imagecopy($cut, $this->image, 0, 0, $dst_x, $dst_y, $src_im_w, $src_im_h); 
		imagecopy($cut, $src_im->image, 0, 0, ($src_im_w*$select_char), 0, $src_im_w, $src_im_h); 
		imagecopymerge($this->image, $cut, $dst_x, $dst_y, 0, 0, $src_im_w, $src_im_h, $pct); 
	}
	
	/**
	 * normalImageCopyMerge() is use to marge two images to any direction using the given angle.
	 * ARG1($src_im) is the source image 
	 * ARG2($dst_x) shows positioning of image2(src_img) on $image1(obj_img) [axis-x]
	 * ARG3($dst_y) shows positioning of image2(src_img) on $image1(obj_img) [axis-y]
	 * ARG4($src_x) image2(src_x) x-aixs positioning. such as Image2 actual x-aixs size is 600px where Image2 have 3 images. 
	 *				so we want to select image2.1 then src_x = 0; if image2.2 then src_x = 200; if image2.3 then src_x = 400; 				
	 * ARG5($src_y) image2(src_y) y-aixs positioning. such as Image2 actual y-aixs size is 100px where Image2 have 3 images(ALL IMAGES HAVE SAME HEIGHT). 
	 * ARG6($src_w) image2(src_w) width is 600px; then image2's sprit image2.[1,2,3] width: 200px(WHERE ALL IMAGES HAVE SAME WIDTH). 
	 * ARG7($src_h) image2(src_h) height is 100px then image2's sprit image2.[1,2,3] width: 100px(WHERE ALL IMAGES HAVE SAME HEIGHT).
	 * ARG8($pct) shows image2(src_img) alpha on image1(obj_img)
	 * Example: 
	 *		1. $image = new Image('bucket-angry-sprit.png'); //[600*100]px SIZE
	 *		2. $bg_img= new Image('back-ground.png');
	 *		3. $image->imagecopymerge_alpha($bg_img, 20, 50,   0,   0,   200, 100, 100); //SHOW IMAGE2.1   
	 *		4. $image->imagecopymerge_alpha($bg_img, 20, 50, 200, 0, 200, 100, 100); //SHOW IMAGE2.1   
	 *		5. $image->imagecopymerge_alpha($bg_img, 20, 50, 400, 0, 200, 100, 100); //SHOW IMAGE2.1   
	 *		6. $image->show();
	 *		CREATE A PATTERN FOR LOGIC
	 *		1. imagecopymerge_alpha($image1, $image2, 20, 20, (($image2->width/4)*0), 0, ($image2->width/4), ($image2->height), 100);
	 *		2. imagecopymerge_alpha($image1, $image2, 20, 20, (($image2->width/4)*1), 0, ($image2->width/4), ($image2->height), 100);
	 *		3. imagecopymerge_alpha($image1, $image2, 20, 20, (($image2->width/4)*2), 0, ($image2->width/4), ($image2->height), 100);
	 *		4. imagecopymerge_alpha($image1, $image2, 20, 20, (($image2->width/4)*3), 0, ($image2->width/4), ($image2->height), 100);
	 */	
	function normalImageCopyMerge($src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
		$cut = imagecreatetruecolor($src_w, $src_h);
		imagecopy($cut, $this->image, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
		imagecopy($cut, $src_im->image, 0, 0, $src_x, $src_y, $src_w, $src_h); 
		imagecopymerge($this->image, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 	
	}
	
	
	/**
	 * Mirrors the given image in the desired way.
	 * Arguments : $type - Direction of mirroring. This can be 1(Horizondal Flip), 2(Vertical Flip) or 3(Both Horizondal and Vertical Flip)
	 * Example: $img = new Image("file.png"); $img->flip(2); $img->show();
	 */
	function flip($type) {
		if(!$this->image) return false;
		if(!$type) return false;
		
		$imgdest= imagecreatetruecolor($this->width, $this->height);
		$imgsrc	= $this->image;
		$height	= $this->height;
		$width	= $this->width;

		switch( $type ) {
			//Mirroring direction
			case IMAGE_FLIP_HORIZONTAL:
			case 'h':
				for( $x=0 ; $x<$width ; $x++ )
					imagecopy($imgdest, $imgsrc, $width-$x-1, 0, $x, 0, 1, $height);
				break;

			case IMAGE_FLIP_VERTICAL:
			case 'v':
				for( $y=0 ; $y<$height ; $y++ )
					imagecopy($imgdest, $imgsrc, 0, $height-$y-1, 0, $y, $width, 1);
				break;

			case IMAGE_FLIP_BOTH:
			case 'b':
				for( $x=0 ; $x<$width ; $x++ )
					imagecopy($imgdest, $imgsrc, $width-$x-1, 0, $x, 0, 1, $height);

				$rowBuffer = imagecreatetruecolor($width, 1);
				for( $y=0 ; $y<($height/2) ; $y++ ) {
					imagecopy($rowBuffer, $imgdest  , 0, 0, 0, $height-$y-1, $width, 1);
					imagecopy($imgdest  , $imgdest  , 0, $height-$y-1, 0, $y, $width, 1);
					imagecopy($imgdest  , $rowBuffer, 0, $y, 0, 0, $width, 1);
				}

				imagedestroy( $rowBuffer );
				break;
			}
		
		$this->image = $imgdest;
		return $this;
	}
	
	/**
	 * flipHorigontal() use for image flip on horigotal
	 * Agruments :  1. fliped image width
	 *				2. fliped image height
	 * Example: 
	 * 1. $image = new Image('bucket-angry.png');
	 * 2. $image->flipHorigontal();
	 * 3. $image->show();	
	 */	
	function flipHorigontal($width, $height) {
		if(!$this->image) return false;

		$imgdest= imagecreatetruecolor($this->width, $this->height);
		$imgsrc	= $this->image;
		$width	= $width;
		$height	= $height;
		
		imagecolortransparent($imgdest, imagecolorallocate($imgdest, 0, 0, 0));
		imagealphablending($imgdest, false);
		imagesavealpha($imgdest, true);
		$chk = imagecopyresampled($imgdest, $this->image, 0, 0, ($width-1), 0, $width, $height, 0-$width, $height);

		if($chk) {
			$this->image = $imgdest;
			return $this;
		} else {
			return false;
		}		
	}
	
	/**
	 * Resize the image to an new size. Size can be specified in the arugments.
	 * Agruments :$new_width - The width of the desired image. If 0, the function will automatically calculate the width using the height ratio.
	 *			  $new_width - The width of the desired image. If 0, the function will automatically calculate the value using the width ratio.
	 *			  $use_resize- If true, the function uses imagecopyresized() function instead of imagecopyresampled(). 
	 *					Resize is faster but poduces poorer quality image. Resample on the other hand is slower - but makes better images.
	 * Example: $img -> resize(60, 0, false); // Better quality image created using width ratio
	 * 			$img -> resize(120, 300);
	 */
	function resize($new_width,$new_height, $use_resize = true) {
		if(!$this->image) return false;
		if(!$new_height and !$new_width) return false; //Both width and height is 0
		
		$height = $this->height;
		$width  = $this->width;
		
		//If the width or height is give as 0, find the correct ratio using the other value
		if(!$new_height and $new_width) $new_height = $height * $new_width / $width; //Get the new height in the correct ratio
		if($new_height and !$new_width) $new_width	= $width  * $new_height/ $height;//Get the new width in the correct ratio

		//Create the image
		$new_image = imagecreatetruecolor($new_width,$new_height);
		imagealphablending($new_image, false);
		if($use_resize) imagecopyresized($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		else imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		
		$this->image = $new_image;
		return $this;
	}

	/** 
	 * Crops the given image from the ($from_x,$from_y) point to the ($to_x,$to_y) point.
	 * Arguments :$from_x - X coordinate from where the crop should start
	 *			  $from_y - Y coordinate from where the crop should start
	 *			  $to_x   - X coordinate from where the crop should end
	 *			  $to_y   - Y coordinate from where the crop should end
	 * Example: $img -> crop(250,200,400,250);
	 */
	function crop($from_x,$from_y,$to_x,$to_y) {
		if(!$this->image) return false;
		
		$height = $this->height;
		$width  = $this->width;

		$new_width  = $to_x - $from_x;
		$new_height = $to_y - $from_y;
		//Create the image
		$new_image = imagecreatetruecolor($new_width, $new_height);
		imagealphablending($new_image, false);
		imagecopy($new_image, $this->image, 0,0, $from_x,$from_y, $new_width, $new_height);
		$this->image = $new_image;
		
		return $this;
	}

	/**
	 * Save the image to the given file. You can use this function to convert image types to. Just specify the image format you want as the extension.
	 * Argument:$file_name - the file name to which the image should be saved to
	 * Returns: false if save operation fails.
	 * Example: $img->save("image.png"); 
	 * 			$image->save('file.jpg');
	 */
	function save($file_name, $destroy = true) {
		if(!$this->image) return false;
		
		$extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
		
		switch($extension) {
			case 'png' : return imagepng($this->image, $file_name); break;
			case 'jpeg': 
			case 'jpg' : return imagejpeg($this->image, $file_name); break;
			case 'gif' : return imagegif($this->image, $file_name); break;
			default: break;
		}
		if($destroy) $this->destroy();
		return false;
	}
	
    /** 
	 * simple function that calculates the *exact* bounding box (single pixel precision). 
     * The function returns an associative array with these keys: 
     * left, top:  coordinates you will pass to imagettftext 
     * width, height: dimension of the image you have to create 
     */ 
	function calculateTextBox($text) { 
		$rect = imagettfbbox($this->font_size, $this->text_angle, $this->font_ttf, $text); 
		$minX = min(array($rect[0],$rect[2],$rect[4],$rect[6])); 
		$maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6])); 
		$minY = min(array($rect[1],$rect[3],$rect[5],$rect[7])); 
		$maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
		return array( 
			"left"   => abs($minX) - 1, 
			"top"    => abs($minY) - 1, 
			"width"  => $maxX - $minX, 
			"height" => $maxY - $minY, 
			"box"    => $rect 
		);		
	}
	
	
	function createCalloutRectangle($co_x, $co_y, $bgcolor, $txtcolor, $txtstring, $co_indct, $max) {
		$strArr = $this->calloutString($txtstring, $max);
		$ARRSIZE= count($strArr);
		
		$txtstring = implode("\r\n", $strArr);
		$the_box   = $this->calculateTextBox($txtstring); 

		$padding = (IMAGE_STANDARD_WIDTH  < $the_box["width"] ) ? 5 : (IMAGE_STANDARD_WIDTH/8); 
		
		$im_w 	 = (IMAGE_STANDARD_WIDTH  < $the_box["width"] ) ? $the_box["width"] + $padding : IMAGE_STANDARD_WIDTH + $padding;
		$im_h 	 = (IMAGE_STANDARD_HEIGHT < $the_box["height"]) ? $the_box["height"] + $padding : (($ARRSIZE > 1) ? IMAGE_STANDARD_HEIGHT + $padding : IMAGE_STANDARD_HEIGHT);
		
		$pos_x = 0; $pos_y = 0;
		switch($co_indct) {
			case LEFT_SIDE:
			case 'l':
				$rpos = $im_w * 5 / 100; $lpos = 0;
				$pos_x = ($im_w/4); $pos_y = 20;
			break;			
			case MID_SIDE:
			case 'm':
				$rpos = $im_w * 5 / 100; $lpos = $im_w * 5 / 100;			
				$pos_x = ($im_w/2); $pos_y = 20;
			break;			
			case RIGHT_SIDE:
			case 'r':
				$rpos = 0; $lpos = $im_w * 5 / 100;						
				$pos_x = ($im_w/4)+($im_w/2); $pos_y = 40;	
			break;
		}
		$values = array(($co_x), ($co_y),   								// Point 1 (x1, y1)
						($co_x + $im_w), ($co_y),   						// Point 2 (x2, y2)
						($co_x + $im_w), ($co_y + $im_h),  					// Point 3 (x3, y3)
						($co_x + $im_w)-$pos_x+$lpos, ($co_y + $im_h),  	// Point 4 (x4, y4)
						($co_x + $im_w)-$pos_x, ($co_y + $im_h)+$pos_y, 	// Point 5 (x5, y5) if x5 > 130 right side; x5 < 130 right side
						($co_x + $im_w)-$pos_x-$rpos, ($co_y + $im_h),  	// Point 6 (x6, y6)
						($co_x),  ($co_y + $im_h));   						// Point 7 (x7, y7)
		#$co_bg_color = imagecolorallocate($this->image, 115, 115, 155); 			
		#$co_txt_color= imagecolorallocate($this->image, 255, 255, 255);
		imagefilledpolygon($this->image, $values, 7, $bgcolor);
		imagettftext($this->image, $this->font_size, $this->text_angle, $co_x + $padding, ($co_y + $the_box["top"]) + $padding, $txtcolor, $this->font_ttf, $txtstring); 	
	}
	
	function calloutString($string, $limit) {
		$string = trim(preg_replace("/\s+/"," ",$string)); 
		$word_array = explode(" ", $string); 
		$new_str_array = Array(); $tmp_array = Array();	
		$tmp_array[] = $this->convertString($word_array[0], $limit);

		foreach($word_array as $key => $val) {
			$lenght = ($key == 0) ? strlen(implode(' ', $tmp_array)) : strlen(implode(' ', $tmp_array)) + strlen($word_array[$key]);				
			if($lenght > $limit) {
				$new_str_array[] = implode(' ', $tmp_array);	
				unset($tmp_array);
			}
			if($key != 0) 
				$tmp_array[] = $this->convertString($val, $limit); 
		}
		$new_str_array[] = implode(' ', $tmp_array);	
		//return implode("\r\n", $new_str_array);
		return $new_str_array;
	}

	function convertString($str, $limit) {
		if(strlen($str) > $limit) {
			$temp = substr($str, 0, $limit-5);
			return $temp.' ...';
		} else {
			return $str;
		}
	}
	
	
	
	/**
	 * Display the image and then destroy it.
	 * Example: $img->show();
	 */
	function show($destroy = true) {
		if(!$this->image) return false;
		
		header("Content-type: ".$this->info['mime']);
		switch($this->info['mime']) {
			case 'image/png' : imagepng($this->image); break;
			case 'image/jpeg': imagejpeg($this->image); break;
			case 'image/gif' : imagegif($this->image); break;
			default: break;
		}
		if($destroy) $this->destroy();
		
		return $this;
	}
	
	/**
	 * Discard any changes made to the image and restore the original state
	 */
	function restore() {
		$this->image = $this->org_image;
		return $this;
	}
	
	/**
	 * Destroy the image to save the memory. Do this after all operations are complete.
	 */
	function destroy() {
		 imagedestroy($this->image);
		 imagedestroy($this->org_image);
	}
}
?>