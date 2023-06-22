<!DOCTYPE html>
<style>

</style>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Essential</title>
    <link rel="stylesheet" href="./styles/Clientes.css">
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
        <div style="display: flex; justify-content:space-between;">
            <div id="divfiltro">
                <input name="filter" title="Pressione a tecla enter para buscar por um cliente" id="filter" autocomplete="off" required />
                <label for="filter">
                    <span><i class="fa fa-magnifying-glass"></i></span>
                </label>
            </div>
            <div id="btnAddCustomers" title="Adicionar Cliente" onclick="addCustomer()">
                <i id="iconAddCustomer" class="fa fa-plus"></i>
            </div>
        </div>
        <table id="relatorio_clientes">

        </table>
    </div>

    <div id="modalAddCustomer" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Cadastrar Cliente</h2>
            <form id="formAddCustomer" autocomplete="off">
                <input type="text" hidden id="hdId" name="hdId">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" required>

                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" maxlength="14" onkeydown="mCPF(this.value, false)">

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <label for="celular">Celular:</label>
                <input type="text" id="celular" name="celular" minlength="15" maxlength="15" required onkeydown="mCEL(this.value, false)">

                <label for="imageInput">Foto:</label>
                <input type="file" id="imageInput" accept=".jpeg, .jpg, .png" required>

                <button id="btnSendForm" type="submit">Cadastrar</button>
            </form>
        </div>
    </div>
</body>

</html>

<script>
    window.onload = (event) => {
        listarClientes();
    };

    function listarClientes(filter = null) {
        $.ajax({
            url: '../index.php',
            type: 'GET',
            data: {
                filter: filter,
                action: 'getCustomers'
            },
            success: function(response) {
                const clientes = JSON.parse(response);

                let listaClientes = `<tr>
                                        <th style="text-align:center;"></th>
                                        <th style="text-align:center;">ID</th>
                                        <th style="text-align:center;">Nome</th>
                                        <th style="text-align:center;">CPF</th>
                                        <th style="text-align:center;">Email</th>
                                        <th style="text-align:center;">Telefone</th>
                                        <th style="text-align:center;">Ações</th>
                                    </tr>`

                if (clientes.length > 0) {
                    clientes.forEach((element) => {
                        listaClientes += `<tr>
                                            <td style="height:3vw; width: 4vw;"><img src="${(element.foto != '') ? '../'+element.foto : '../assets/user-no-image.png'}" alt="image_user"></td>
                                            <td style="height:3vw; text-align:center;">${element.id}</td>
                                            <td style="height:3vw; text-align:center;">${element.nome}</td>
                                            <td style="height:3vw; text-align:center;">${mCPF(element.cpf)}</td>
                                            <td style="height:3vw; text-align:center;">${element.email}</td>
                                            <td style="height:3vw; text-align:center;">${mCEL(element.celular)}</td>
                                            <td style="height:3vw; text-align:center;"><i style="color:#ff0000; cursor:pointer;" class="fa fa-trash" onclick="deleteCustomer('${element.id}')" title="Exluir Cliente"></i><i style="color:#4e7bff; cursor:pointer; margin-left: 15px;" class="fa fa-pencil" onclick="addCustomer('${element.id}')" title="Editar Cliente"></i></td>
                                        </tr>`;
                    });
                } else {
                    listaClientes += `<tr><td colspan="7" style="text-align:center;">Nenhum cliente encontrado.</td></tr>`;
                }

                $("#relatorio_clientes").html(listaClientes);
            },
            error: function(error) {
                console.log(error);
            },
        });
    }

    document.querySelector('#filter').addEventListener('keydown', function(event) {
        if (event.keyCode === 13) {
            listarClientes(document.querySelector('#filter').value)
        }
    });

    function deleteCustomer(id) {
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
                                    $.confirm({
                                        title: 'Sucesso',
                                        content: 'Cliente excluído',
                                        type: 'green',
                                        boxWidth: '20%',
                                        useBootstrap: false,
                                        icon: 'fa fa-check',
                                        typeAnimated: true,
                                        buttons: {
                                            tryAgain: {
                                                text: 'Ok',
                                                action: function() {}
                                            }
                                        }
                                    });
                                } else {
                                    $.confirm({
                                        title: 'Atenção',
                                        content: 'Tivemos um problema ao excluir o cliente, tente novamente mais tarde.',
                                        type: 'orange',
                                        boxWidth: '20%',
                                        useBootstrap: false,
                                        icon: 'fa fa-warning',
                                        typeAnimated: true,
                                        buttons: {
                                            tryAgain: {
                                                text: 'Ok',
                                                action: function() {
                                                }
                                            }
                                        }
                                    });
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

    function addCustomer(customerId, limpar = true) {
        if (customerId) {
            $.ajax({
                url: '../index.php',
                type: 'GET',
                data: {
                    action: 'getCustomers',
                    filter: customerId
                },
                success: function(response) {
                    const cliente = JSON.parse(response);
                    cliente.forEach(element => {
                        document.getElementById('nome').value = element.nome;
                        document.getElementById('cpf').value = mCPF(element.cpf);
                        document.getElementById('email').value = element.email;
                        document.getElementById('celular').value = mCEL(element.celular);
                        document.getElementById('hdId').value = customerId;
                    });
                    document.getElementById("btnSendForm").innerText = 'Editar'
                    document.getElementById("imageInput").removeAttribute("required")
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            if(limpar){
                document.getElementById('hdId').value = '';
                document.getElementById('nome').value = '';
                document.getElementById('cpf').value = '';
                document.getElementById('email').value = '';
                document.getElementById('celular').value = '';
                document.getElementById("btnSendForm").innerText = 'Cadastrar'
            }
            document.getElementById("imageInput").setAttribute("required", true)
        }
        document.getElementById("modalAddCustomer").style.display = "block";

        var closeButton = document.getElementsByClassName("close")[0];

        closeButton.onclick = function() {
            document.getElementById("modalAddCustomer").style.display = "none";
            document.getElementById('hdId').value = '';
            document.getElementById('nome').value = '';
            document.getElementById('cpf').value = '';
            document.getElementById('email').value = '';
            document.getElementById('celular').value = '';
        };
    }

    document.getElementById("formAddCustomer").addEventListener("submit", function(event) {
        event.preventDefault();

        var nome = document.getElementById("nome").value;
        var cpf = document.getElementById("cpf").value;
        var email = document.getElementById("email").value;
        var celular = document.getElementById("celular").value;
        var fotoInput = document.getElementById("imageInput");
        var id = document.getElementById("hdId").value;
        var foto = fotoInput.files[0];

        if (!validateCPF(cpf)) {
            $.confirm({
                title: 'Atenção',
                content: 'Informe um CPF válido para prosseguir com o cadastro do cliente.',
                type: 'orange',
                boxWidth: '20%',
                useBootstrap: false,
                icon: 'fa fa-warning',
                typeAnimated: true,
                buttons: {
                    tryAgain: {
                        text: 'Ok',
                        action: function() {
                            addCustomer(document.getElementById("hdId").value, false)
                        }
                    }
                }
            });
            return;
        }

        var formData = new FormData();
        formData.append('nome', nome);
        formData.append('cpf', cpf.replace(/\D/g, ""));
        formData.append('email', email);
        formData.append('celular', celular.replace(/\D/g, ""));
        formData.append('foto', foto);
        formData.append('id', id);
        formData.append('action', 'addCustomer');

        $.ajax({
            url: '../index.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const retorno = JSON.parse(response);
                console.log(retorno.status, retorno.mensagem);
                if (retorno && retorno.status == 1) {
                    $.confirm({
                        title: 'Sucesso',
                        content: `${retorno.mensagem}`,
                        type: 'green',
                        boxWidth: '20%',
                        useBootstrap: false,
                        icon: 'fa fa-check',
                        typeAnimated: true,
                        buttons: {
                            tryAgain: {
                                text: 'Ok',
                                action: function() {
                                }
                            }
                        }
                    });
                } else {
                    $.confirm({
                        title: 'Atenção',
                        content: `${retorno.mensagem}`,
                        type: 'orange',
                        boxWidth: '20%',
                        useBootstrap: false,
                        icon: 'fa fa-warning',
                        typeAnimated: true,
                        buttons: {
                            tryAgain: {
                                text: 'Ok',
                                action: function() {
                                    addCustomer(document.getElementById("hdId").value, false)
                                }
                            }
                        }
                    });
                }
                listarClientes();
            },
            error: function(error) {
                $.confirm({
                    title: 'Atenção',
                    content: 'Tivemo um problema ao efetuar o cadastro, tente novamente mais tarde.',
                    type: 'orange',
                    boxWidth: '20%',
                    useBootstrap: false,
                    icon: 'fa fa-warning',
                    typeAnimated: true,
                    buttons: {
                        tryAgain: {
                            text: 'Ok',
                            action: function() {
                            }
                        }
                    }
                });
            }
        });

        document.getElementById("modalAddCustomer").style.display = "none";
    });

    function validateCPF(cpf) {
        cpf = cpf.replace(/\D/g, ""); // Remove caracteres não numéricos

        // Verifica se o CPF possui 11 dígitos
        if (cpf.length !== 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais, o que é inválido
        if (/^(\d)\1+$/.test(cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        let sum = 0;
        for (let i = 0; i < 9; i++) {
            sum += parseInt(cpf.charAt(i)) * (10 - i);
        }
        let digit1 = 11 - (sum % 11);
        if (digit1 > 9) {
            digit1 = 0;
        }

        // Verifica o primeiro dígito verificador
        if (parseInt(cpf.charAt(9)) !== digit1) {
            return false;
        }

        // Calcula o segundo dígito verificador
        sum = 0;
        for (let i = 0; i < 10; i++) {
            sum += parseInt(cpf.charAt(i)) * (11 - i);
        }
        let digit2 = 11 - (sum % 11);
        if (digit2 > 9) {
            digit2 = 0;
        }

        // Verifica o segundo dígito verificador
        if (parseInt(cpf.charAt(10)) !== digit2) {
            return false;
        }

        return true;
    }

    function mCPF(cpf, relatorio = true) {
        if (cpf != '') {
            cpf = cpf.replace(/\D/g, "")
            cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
            cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2")

            //adicionei o parametro relatorio pra saber como retornar o valor mascarado.
            if (relatorio) {
                return cpf
            } else {
                document.getElementById("cpf").value = cpf;
            }
        } else {
            return ' - '
        }
    }

    function mCEL(cel, relatorio = true) {
        if (cel != '') {
            cel = cel.replace(/\D/g, '')
            cel = cel.replace(/(\d{2})(\d)/, "($1) $2")
            cel = cel.replace(/(\d)(\d{4})$/, "$1-$2")

            //adicionei o parametro relatorio pra saber como retornar o valor mascarado.
            if (relatorio) {
                return cel
            } else {
                document.getElementById("celular").value = cel;
            }
        } else {
            return ' - '
        }
    }
</script>