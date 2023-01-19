<?php
include("simple_html_dom.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        img {
            max-width: 100%;
        }

        .axar-container {
            max-width: 1000px;
            margin: auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 1rem;
        }

        .axar-container a {
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.5s ease;
        }

        .axar-container a:hover {
            box-shadow: 0 4px 4px 4px #6666;
            transform: scale(1.05);
        }

        .axar-container>img {
            opacity: 0.25;
        }
    </style>
</head>

<body>

    <div class="axar-container">
        <?php

        $links = [
            "https://istari.ru/katalog-knig/newitems/",
            "https://xlm.ru/manga"
        ];

        $new = [];

        foreach ($links as $link) {
            $html = file_get_html($link);

            // для istari.ru
            foreach ($html->find("#catalog_items .item") as $item) {
                $href = "https://istari.ru" . $item->find("a")[0]->href;    // https://istari.ru/katalog-knig/manga/istari-comics/memuary-vanitasa/memuary-vanitasa-tom-6/
                $imgUrl = "https://istari.ru" . $item->find(".item_preview_img > img")[0]->src;
                $no_quantity = $item->find(".no_quantity")[0];

                $newManga = [];
                $newManga["href"] = $href;
                $newManga["src"] = $imgUrl;

                if (!$no_quantity) {
                    array_push($new, $newManga);
                };
            };

            // для xlm.ru
            foreach ($html->find(".container") as $e) {
                $title = $e->find(".title");
                if (count($title) != 0) {
                    $mangas = $e->find(".title + .columns")[0]->find(".column");
                    foreach ($mangas as $manga) {
                        if (count($manga->find(".label-icon.new")) != 0) {
                            $newManga = [];
                            $href = $manga->find(".product-name")[0]->href;
                            $dsrc = "data-src";
                            $imgUrl = "https://xlm.ru/" . $manga->find("img")[0]->$dsrc;

                            $newManga["href"] = $href;
                            $newManga["src"] = $imgUrl;
                            array_push($new, $newManga);
                        };
                    };
                };
            };

            // Todo:
            // Местное хранилище (Локальный файл или БД)
            // повторить для xlm.ru
            // Подключить TG API
        };

        $conn = mysqli_connect("localhost", "axar", "parsee123", "axar");

        foreach ($new as $manga) {
            $href = $manga["href"];
            $src = $manga["src"];
            //echo "<a href='$href'><img src='$src'/></a>";

            $allManga = $conn->query("SELECT * FROM Manga WHERE href='$href'");

            //$sql = "INSERT INTO Manga (href, src) VALUES ('$href', '$src')";
            //if ($conn->query($sql) !== TRUE) {
                //echo "Error: " . $sql . "<br>" . $conn->error;
            //};

        };

        mysqli_close($con);

        ?>
    </div>

</body>

</html>