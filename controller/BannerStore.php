<?php
session_start();
$title = $_REQUEST['title'] ?? null;
$detail = $_REQUEST['detail'] ?? null;
$ctaTitle = $_REQUEST['ctaTitle'] ?? null;
$ctaLink = $_REQUEST['ctaLink'] ?? null;
$videoLink = $_REQUEST['videoLink'] ?? null;
$bannerImage = $_FILES['bannerImage'] ?? null;
$extension = pathinfo($bannerImage['name'], PATHINFO_EXTENSION) ?? null;

$acceptExtensions = ['jpg', 'png','svg'];
$errors = [];

// Validation
if (empty($title)) {
  $errors['title_error'] = "Title is missing";
}

if (empty($detail)) {
  $errors['detail_error'] = "Detail is missing";
}

if ($bannerImage['size'] == 0) {
  $errors['bannerImage_error'] = "Banner Image is missing";
} elseif (!in_array($extension, $acceptExtensions)) {
  $errors['bannerImage_error'] = "$extension is not acceptable! Acceptable extensions: " . join(', ', $acceptExtensions);
}

if (count($errors) > 0) {
  $_SESSION['errors'] = $errors;
  header('Location: ../dashboard/banner.php');
} else {
  $fileName = 'Banner-' . uniqid() . '.' . $extension;
  move_uploaded_file($bannerImage['tmp_name'], '../Uploads/' . $fileName);
  $UploadPath = "Uploads/$fileName";

  include('../database/env.php');
  $query = "INSERT INTO banners(title, detail, cta_title, cta_link, video_link, banner_img) VALUES ('$title','$detail','$ctaTitle','$ctaLink', '$videoLink', '$UploadPath')";

  $res = mysqli_query($connection, $query);

  if ($res) {
    $_SESSION['auth']['title'] = $title;
    $_SESSION['auth']['detail'] = $detail;
    $_SESSION['auth']['cta_link'] = $ctaLink;
    $_SESSION['auth']['cta_title'] = $ctaTitle;
    $_SESSION['auth']['video_link'] = $videoLink;
    $_SESSION['success'] = true;

    if ($bannerImage['size'] > 0) {
      $_SESSION['auth']['BannerImage'] = $UploadPath;
    }
    header('Location: ../dashboard/Profile.php');
  }
}
?>