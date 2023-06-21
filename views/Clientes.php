<!DOCTYPE html>
<style>
    #table_users {
        width: 95%;
        height: 100%;
        background-color: #fff;
        border-radius: 8px;
        margin: auto;
        margin-top: 8vw;
        padding: 20px;
    }

    #divfiltro {
        overflow: hidden;
        position: relative;
        width: 15%;
    }
</style>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Essential</title>
    <link rel="stylesheet" href="./styles/global.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/8072e123c8.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
</head>

<body>
    <header>
        <img src="../assets/logo.png" alt="logo">
    </header>
    <div id="table_users">
        <div id="divfiltro">
            <input name="filter" autocomplete="off" required />
            <label for="filter">
                <span><i class="fa fa-magnifying-glass"></i></span>
            </label>
            <!-- <i class="fa fa-magnifying-glass"></i> -->
        </div>
        <table id="relatorio_clientes">

        </table>
    </div>
</body>

</html>

<script>
    window.onload = (event) => {
        listarClientes();
    };

    function listarClientes() {
        $.ajax({
            url: '../index.php',
            type: 'GET',
            data: {
                action: 'getCustomers'
            },
            success: function(response) {
                console.log(response);
                const clientes = JSON.parse(response);
                let listaClientes = '';

                if (clientes.length > 0) {
                    clientes.forEach((element) => {
                        listaClientes += `<tr>
                                            <td style="height:3vw; width: 4vw;"><img src="../assets/user-no-image.png" alt="image_user"></td>
                                            <td style="height:3vw; text-align:center;">${element.nome}</td>
                                            <td style="height:3vw; text-align:center;">${mCPF(element.cpf)}</td>
                                            <td style="height:3vw; text-align:center;">${element.email}</td>
                                            <td style="height:3vw; text-align:center;">${mCEL(element.telefone)}</td>
                                            <td style="height:3vw; text-align:center;"><i style="color:#ff0000; cursor:pointer;" class="fa fa-trash" onclick="excluirCliente('${element.id}')" title="Exluir Cliente"></i><i style="color:#4e7bff; cursor:pointer; margin-left: 15px;" class="fa fa-pencil" onclick="editarCliente('${element.id}')" title="Editar Cliente"></i></td>
                                        </tr>`;
                    });
                } else {
                    listaClientes = `<tr><td colspan="6" style="text-align:center;">Nenhum cliente encontrado.</td></tr>`;
                }

                $("#relatorio_clientes").html(listaClientes);
            },
            error: function(error) {
                console.log(error);
            },
        });
    }

    function excluirCliente(id) {
        $.confirm({
            title: 'Atenção!',
            icon: 'fa fa-warning',
            boxWidth: '30%',
            useBootstrap: false,
            content: 'Você tem certeza que deseja excluir esse cliente?',
            type: 'orange',
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: 'Sim',
                    btnClass: 'btn-red',
                    action: function() {
                        $.ajax({
                            url: '../index.php',
                            type: 'POST',
                            data: {
                                action: 'deleteCustomer',
                                id: id
                            },
                            success: function(response) {
                                const exclusao = JSON.parse(response);
                                if (exclusao) {
                                    listarClientes();
                                    console.log('Cliente ' + id + ' excluído com sucesso');
                                } else {
                                    console.log('Erro ao excluir o cliente ' + id);
                                }
                            },
                            error: function(error) {
                                console.log(error);
                            },
                        });
                    },
                },
                close: {
                    text: 'Não',
                    action: function() {},
                },
            },
        });
    }

    function mCPF(cpf) {
        if (cpf != '') {
            cpf = cpf.replace(/\D/g, "")
            cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
            cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2")
            return cpf
        } else {
            return ' - '
        }
    }

    function mCEL(cel) {
        if (cel != '') {
            cel = cel.replace(/\D/g, '')
            cel = cel.replace(/(\d{2})(\d)/, "($1) $2")
            cel = cel.replace(/(\d)(\d{4})$/, "$1-$2")
            return cel
        } else {
            return ' - '
        }
    }
</script>