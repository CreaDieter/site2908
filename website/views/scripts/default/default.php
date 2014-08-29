<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Example</title>
</head>

<body>

	<style type="text/css">
		body {
			padding:0;
            margin: 0;
			font-family: "Lucida Sans Unicode", Arial;
			font-size: 14px;
		}

       #site {
           margin: 0 auto;
           width: 600px;
           padding: 30px 0 0 0;
       }

		h1, h2, h3 {
			font-size: 18px;
			padding: 0 0 5px 0;
            border-bottom: 1px solid #001428;
            margin-bottom: 5px;
		}

        h3 {
            font-size: 14px;
            padding: 15px 0 5px 0;
            margin-bottom: 5px;
            border-color: #cccccc;
        }

		p {
			padding: 0 0 5px 0;
		}
		
		a {
			color: #000;
		}

        .content-myTextarea {
            color: #0464BB;
            font-weight: bold;
            font-style: italic;
            text-shadow: 1px 1px #cccccc;
        }

        strong {
            font-weight: bold;
            color: #005c24;
        }

        #logo {
            text-align: center;
            padding: 0 0 10px 0;
        }

        #site ul {
            padding: 10px 0 10px 20px;
            list-style: circle;
        }

	</style>


    <div id="site">
        <div id="logo">
            <img src="/pimcore/static/img/logo-gray.png" />
        </div>

        <h1>Hello World!</h1>
        <p>
            This website has yet to be setup by Studio Emma
			<?=$this->input('title')?>
			<?php
			if (!$this->sebasicPluginInstalled) { ?>
				</p>
				<p>
				<strong>Please enable SEBasic Plugin First!</strong>
				</p>
				<p>
			<?php
			}
			?>

            <br /><br />
            To learn how to create templates with pimcore, please visit our <a href="http://www.pimcore.org/wiki/" target="_blank">documentation</a> or install the example data package.
            <br />
            <br />
        </p>
    </div>

</body>
</html>