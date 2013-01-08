<?php
/*********************************
 *Pep-Talk(Software Lab). Copyright © Nitin Soni. All Rights Reserved.
 *join us:https://www.facebook.com/peptalk.softwarelab
 **********************************/

include("Image.php");

$page_img = new Image('comic_page.PNG');

$image = new Image('003.jpg');
$image->crop(0, 0, 295, 230);

$image->save("temp.jpg");


$image1 = new Image("temp.jpg");

$image1->font_ttf  = 'comic.ttf';
$image1->font_size = 8;
$image1->text_angle= 0;
$text_string1 = "Dear friends, i will started a new project which are open source...";
$text_string2 = "WOW... thanks to pep-talk...";
//$text_string2 = "Hey...";


$image2 = new Image('bucket-angry.png');
$image2->resize($image2->width/2-30, $image2->height/2-30, false);
$image1->normalImageCopyMerge($image2, 40, 90, 0, 0, $image2->width/2-30, $image2->height/2-30, 100); #CORRECT
$image2->flipHorigontal($image2->width/2-30, $image2->height/2-30);
$image1->normalImageCopyMerge($image2, 190, 90, 0, 0, $image2->width/2-30, $image2->height/2-30, 100); #CORRECT
$co_bg_color1 = imagecolorallocate($image1->image, 0, 0, 0);
$co_txt_color1= imagecolorallocate($image1->image, 255, 255, 255);
$image1->createCalloutRectangle(10, 0, $co_bg_color1, $co_txt_color1, $text_string1, 3, 40);

$image3 = new Image('bucket-exclaiming.png');
$image3->resize($image3->width/2-30, $image3->height/2-30, false);
$image1->normalImageCopyMerge($image3, 18, 119, 0, 0, $image3->width/2-30, $image3->height/2-30, 100); #CORRECT
$image3->flipHorigontal($image3->width/2-30, $image3->height/2-30);
$image1->normalImageCopyMerge($image3, 162, 119, 0, 0, $image3->width/2-30, $image3->height/2-30, 100); #CORRECT
$co_bg_color2 = imagecolorallocate($image1->image, 0, 0, 0);
$co_txt_color2= imagecolorallocate($image1->image, 255, 255, 255);
$image1->createCalloutRectangle(150, 55, $co_bg_color2, $co_txt_color2, $text_string2, 2, 25);


#$image1->show();
$page_img->normalImageCopyMerge($image1, 5, 5, 0, 0, $image1->width, $image1->height, 100); #CORRECT
$page_img->normalImageCopyMerge($image1, $image1->width + 10, 5, 0, 0, $image1->width, $image1->height, 100); #CORRECT
$page_img->save("comic.jpg");
$page_img->show();
?>