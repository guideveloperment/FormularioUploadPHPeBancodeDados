    <html>

    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <link rel="stylesheet" href="cadastro.css">
    </head>

    <body>
        <h1>Formulário Upload PHP</h1></br></br>






        <?php
        $mensagem = '';
        $arquivos = [];
        try {
            $sqlite = "sqlite:ProjetoPHP.db";
            // conexão ao sqlite
            $pdo = new PDO($sqlite);
            $consulta = $pdo->query("SELECT NM_ARQUIVO, DS_ARQUIVO, DS_EXTENSAO FROM TB_ARQUIVO;");
            $arquivos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
        if (isset($_POST['enviar-formulário'])) {


            $formatosPermitidos = array("png", "jpeg", "jpg", "gif", "mkv", "wmv", "mov", "avi", "flv", "mpeg", "rmvb", "mp3", "mp4");
            $extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);




            if (in_array($extensao, $formatosPermitidos)) :
                $pasta = "arquivos/";
                $temporario = $_FILES['arquivo']['tmp_name'];
                $novoNome = uniqid() . ".$extensao";
                $descricao = $_POST['descricao'];




                if (move_uploaded_file($temporario, $pasta . $novoNome)) :
                    try {

                        $stmt = $pdo->prepare('INSERT INTO TB_ARQUIVO (NM_ARQUIVO, DS_ARQUIVO, DS_EXTENSAO) VALUES(:nome, :descricao,:extensao)');
                        $stmt->execute(array(
                            ':nome' => $novoNome,
                            ':descricao' => $descricao,
                            ':extensao' => $extensao
                            
                        ));
                    } catch (PDOException $e) {
                        echo 'Error: ' . $e->getMessage();
                    }


                    $mensagem = "upload feito com sucesso!";
                else :
                    $mensagem = "Erro, não foi possível fazer o ulpload!";
                endif;

            else :
                $mensagem = "Formato inválido";
            endif;
        }
        // echo $mensagem;
        ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">

            <div class="row">
                <div class="col-12">
                    <label>Arquivo</label>
                    <input type="file" name="arquivo">
                </div>
                <div class="col-12">
                    <label>Descricao</label>

                    <textarea class="form-control" required name="descricao"></textarea>
                </div>
                <div class="col-12 mt-3">
                    <input class="form-control btn btn-primary" type="submit" name="enviar-formulário">
                </div>
            </div>


        </form>
        <?php echo ($mensagem); ?>

        <hr>
        <table class="table">
            <tr>
                <th>Nome</th>
                <th>Descricao</th>

            </tr>


            <?php



            $formatosImagens = array("png", "jpeg", "jpg", "gif",);
            while ($linha = array_shift($arquivos)) {
                echo ("<tr >");
                if (in_array($linha['DS_EXTENSAO'], $formatosImagens)) {
                    echo ("<td style='text-aligm:center'><img src='arquivos/{$linha['NM_ARQUIVO']}' width='50'/></td>");
                } else {
                    echo ("<td style='text-aligm:center'>{$linha['NM_ARQUIVO']}</td>");
                }
                echo ("<td>{$linha['DS_ARQUIVO']}</td>");
                echo ("</tr>");
            }

            ?>

        </table>


    </html>
    </body>