<!DOCTYPE html>
<html lang="en">

<head>
  <title><?= $title ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="/public/css/main.min.css">
  <?php foreach ($addStyles as $stylePath): ?>
    <link rel="stylesheet" type="text/css" href="<?= $stylePath ?>">
  <?php endforeach ?>
  <script type="text/javascript" src="/public/js/jquery.min.js"></script>
  <script type="text/javascript" src="/public/js/jquery.validate.min.js"></script>
  <script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
</head>

<body>
  <?= $content ?>
</body>

</html>