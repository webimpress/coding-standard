<?php declare(strict_types=1); ?>
<html>
<head>
    <?php echo '<title></title>'; ?>
    <?php if ($now) : ?>
        <link href=""/>
        <style>
            body {
                color: red;
            }
        </style>
    <?php endif; ?>
</head>
<body>
    <h1><?=$title?></h1>

    <?php
    if ($something) {
        $var = 'abc';

        $date = new \MyClass(
            'a',
            'b',
            [
                'hello' => 'world',
                'foo' => 'bar',
            ]
        );
    }
    ?>

    <table>
    <tbody>
        <?php while (true) : ?>
            <tr>
                <th><?=$id?></th>
                <td><?php echo $a ?: $b; ?></td>
                <td>
                    <?php
                    if ($x === 1
                        && $y === 2
                    ) {
                        echo 'hey';
                    } else {
                        echo 'hola';
                    }
                    ?>
                </td>
            </tr>
        <?php endwhile ?>
    </tbody>
    </table>

    <?php
    if ($hello) {
        ?>
        <h2>Hello World!</h2>
        <?php
    }
    ?>
</body>
</html>
