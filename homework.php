<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h3>ВНИМАНИЕ! Перед выполнением убедитесь, что введены правильные найстройки подлключения!!!</h3>


<form name="db" method="post" action="homework.php">
    <input type="hidden" name="start" value="true">
    <input type="submit" name="create_tables" value="Создать таблицы">
    <input type="submit" name="add_foreign_keys" value="Добавить вторичные ключи (связать таблицы)">
    <input type="submit" name="fill_tables" value="Заполнить таблицы">
    <input type="submit" name="get_table" value="Принтануть (join used)">
</form>
</body>
</html>



<?php
if (isset($_POST['start'])){

    $host = "localhost";
    $user = "root";
    $pass = "1111";
    $db = "homework_1";
    $charset = "utf8";
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);

    if(isset($_POST['create_tables'])){

        $query = "
        CREATE TABLE IF NOT EXISTS `product` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `price` int(10) NOT NULL,
          `category_id` int(11) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `category_id` (`category_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        $pdo->query($query);

        $query = "
        CREATE TABLE IF NOT EXISTS `category` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        $pdo->query($query);

        $query = "
        CREATE TABLE IF NOT EXISTS `customer` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `full_name` varchar(255) NOT NULL,
          `phone` varchar(10) NOT NULL,
          `email` varchar(255) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";// да, я знаю, что поля телефон и имейл должны были бы быть уникальными, чтобы не повторялись, но тогда
        //были бы проблемы с автозаполнением, так шо маемо шо маемо. Соре.
        $pdo->query($query);

        $query = "
        CREATE TABLE IF NOT EXISTS `order` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `customer_id` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          `quantity` int(10) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `customer_id` (`customer_id`,`product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        $pdo->query($query);

        echo("Таблицы созданы.");
    }
    if(isset($_POST['add_foreign_keys'])){

        $query = "
        ALTER TABLE `product`
          ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
          ";

        $pdo->query($query);

        $query = "
        ALTER TABLE `order`
          ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ";

        $pdo->query($query);

        echo("Вторичные ключи добавлены.");
    }
    if(isset($_POST['fill_tables'])){

        $names_array = Array("Вова", "Саша", "Денис", "Юра", "Вася", "Петя", "Никита", "Артем");
        $surnames_array = Array("Малиновскиий", "Балан", "Cмельский", "Гармаш", "Кальной", "Хохрин", "Митрофанов", "Зайцев");
        $phones_array = Array("0631388441","0631388442","0631388443","0631388444","0631388445","0631388446","0631388447","0631388448");
        $email_array = Array("email1@mail.ru", "email2@mail.ru", "email3@mail.ru", "email4@mail.ru", "email5@mail.ru", "email6@mail.ru", "email7@mail.ru", "email8@mail.ru");

        for ($i = 0; $i <8; $i++){
            $rand_value = rand(0,7);
            $name = $names_array[$rand_value];
            $rand_value = rand(0,7);
            $surname = $surnames_array[$rand_value];
            $rand_value = rand(0,7);
            $phone = $phones_array[$rand_value];
            $rand_value = rand(0,7);
            $email = $email_array[$rand_value];
            $full_name = $name." ".$surname;

            try {
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $pdo->beginTransaction();
                $query = "
                  INSERT INTO `customer` (`full_name`, `phone`, `email`) 
                  VALUES ('$full_name', '$phone', '$email');
                 ";
                $pdo ->query($query);
                $pdo->commit();

        } catch (Exception $e) {
                $pdo->rollBack();
            echo "Ошибка: " . $e->getMessage();
        }
        }

        $categories_array = Array("Мобильный телефон", "Компьютер", "Планшет");
        for ($i = 0; $i <3; $i++){
            $name = $categories_array[$i];
            try {
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $pdo->beginTransaction();
                $query = "
                INSERT INTO `category` (`name`) 
                VALUES ('$name');
                ";
                $pdo ->query($query);
                $pdo->commit();

            } catch (Exception $e) {
                $pdo->rollBack();
                //echo "Ошибка: " . $e->getMessage();
                //тут я это не принтую, потому что ошибка будет в любом случае - поле name должно быть уникально
                //а если вызывать много раз, то оно не генерирует новое и ошибка
            }
        }

        $productName_array = Array("Apple", "HTC", "Meizu", "Samsung", "OnePlus");
        $price_array = Array("8800", "3500", "22000", "13459", "7222");
        for ($i = 0; $i <8; $i++){
            $rand_value = rand(0,4);
            $productName = $productName_array[$rand_value];
            $rand_value = rand(0,4);
            $price = $price_array[$rand_value];
            $rand_value = rand(1,3);
            $category_id = $rand_value;

            try {
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $pdo->beginTransaction();
                $query = "
                INSERT INTO `product` (`name`, `price`, `category_id`)
                 VALUES ('$productName', '$price', '$category_id');
                ";
                $pdo ->query($query);
                $pdo->commit();

            } catch (Exception $e) {
                $pdo->rollBack();
                echo "Ошибка: " . $e->getMessage();
            }
        }

        //а тут уже посложнее - нужно знать сколько товаров и сколько клиентов.
        $result = $pdo->query("SELECT COUNT(`id`) AS `customers_quantity` FROM `customer`");
        $result = $result->fetch();
        $customers_quantity = $result['customers_quantity'];
        $result = $pdo->query("SELECT COUNT(`id`) AS `products_quantity` FROM `product`");
        $result = $result->fetch();
        $products_quantity = $result['products_quantity'];

        for ($i = 0; $i <8; $i++){
            $customer_id = rand(1,$customers_quantity);
            $product_id = rand(1,$products_quantity);
            $quantity = rand(1,50);

            try {
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $pdo->beginTransaction();
                $query = "
                INSERT INTO `order` (`customer_id`, `product_id`, `quantity`)
                 VALUES ('$customer_id', '$product_id', '$quantity');
                ";
                $pdo ->query($query);
                $pdo->commit();

            } catch (Exception $e) {
                $pdo->rollBack();
                echo "Ошибка: " . $e->getMessage();
            }
        }


            echo("Таблицы заполнены.");
    }
    if(isset($_POST['get_table'])){
        $query = "
        SELECT `order`.`id` AS order_id,`full_name`, `phone`, `product`.`name` AS product_name, `price`,`category`.`name` AS category_name, `quantity`
        FROM `customer`, `product`, `category`, `order`
        WHERE `order`.`customer_id`= `customer`.`id` AND
        `order`.`product_id` = `product`.`id` AND
        `product`.`category_id` = `category`.`id`
        ORDER BY `order`.`id`
        ";

        $result = $pdo->query($query);
        echo("
        <table border='1px solid black'>
        <tr><th>Номер заказа</th><th>ФИО</th><th>Номер телефона</th><th>Название</th><th>Цена</th><th>Категория</th><th>Количество</th></tr>
        ");
        $result->setFetchMode(PDO::FETCH_ASSOC);
        while($row = $result->fetch()){
            echo("<tr>");
            echo ("<td>".$row['order_id']."</td>");
            echo ("<td>".$row['full_name']."</td>");
            echo ("<td>".$row['phone']."</td>");
            echo ("<td>".$row['product_name']."</td>");
            echo ("<td>".$row['price']."</td>");
            echo ("<td>".$row['category_name']."</td>");
            echo ("<td>".$row['quantity']."</td>");
            echo ("</tr>");
        }
        echo("
        </table>
        ");

    }

    //ну вот как-то так.

}



?>