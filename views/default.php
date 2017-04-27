<html>
    <head>
        <title>Default Page</title>
    </head>
    <body>
        <h1><?php echo $name; ?></h1>
        <?php $layout->renderSubView();
            print_r($layout);
        ?>
    </body>
</html>