function mudaAbaCriaRela(aba) {

    if (aba == 'inicio') {
        //ABAS
        document.getElementById('abaSQL').className = 'inactive';
        document.getElementById('abaFiltros').className = 'inactive';
        document.getElementById('abaInicio').className = 'active';
        //CONTEUDO
        document.getElementById('criaRelSQL').style.display = 'none';
        document.getElementById('criaRelFiltros').style.display = 'none';
        document.getElementById('criaRelSQL').style.height = '0px';
        document.getElementById('criaRelFiltros').style.height = '0px';
        document.getElementById('criaRelInicio').style.display = 'block';
    }
    if (aba == 'filtros') {
        //ABAS
        document.getElementById('abaInicio').className = 'inactive';
        document.getElementById('abaSQL').className = 'inactive';
        document.getElementById('abaFiltros').className = 'active';
        //CONTEUDO
        document.getElementById('criaRelSQL').style.display = 'none';
        document.getElementById('criaRelInicio').style.display = 'none';
        document.getElementById('criaRelSQL').style.height = '0px';
        document.getElementById('criaRelInicio').style.height = '0px';
        document.getElementById('criaRelFiltros').style.display = 'block';
    }
    if (aba == 'sql') {
        //ABAS
        document.getElementById('abaInicio').className = 'inactive';
        document.getElementById('abaFiltros').className = 'inactive';
        document.getElementById('abaSQL').className = 'active';
        //CONTEUDO
        document.getElementById('criaRelInicio').style.display = 'none';
        document.getElementById('criaRelFiltros').style.display = 'none';
        document.getElementById('criaRelInicio').style.height = '0px';
        document.getElementById('criaRelFiltros').style.height = '0px';
        document.getElementById('criaRelSQL').style.display = 'block';
    }

}

function mostraMenu(menu) {
    document.getElementById(menu).style.display = 'block';
}

function escondeMenu(menu) {
    document.getElementById(menu).style.display = 'none';
}

function validaConfirmacao() {
    var senha = document.getElementById('senha').value;
    var confirmacao = document.getElementById('confirmasenha').value;

    if (senha != confirmacao) {
        alert("Senhas n\xE3o conferem! Digite novamente para confirmar.");
        document.getElementById('senha').focus();
    }
}