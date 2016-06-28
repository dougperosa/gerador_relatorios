/*function alternaPerguntas(questao, alternarpara) {
 var novaquestao = null;
 var exige = null;
 var preenche = null;
 if (alternarpara.toString() == 'proxima') {
 exige = exigeConsideracoes(questao);
 preenche = respostaBranco(questao);
 if (exige == true) {
 alert('Necessário preencher as considerações dessa questão! (No caso de marcar de 1 à 6)');
 } else if (preenche == true) {
 alert('Necessário preencher alguma opção de resposta!');
 } else {
 novaquestao = questao + 1;
 document.getElementById('questao' + questao.toString()).style.display = 'none';
 document.getElementById('questao' + questao.toString()).style.height = '0px';
 document.getElementById('questao' + novaquestao.toString()).style.display = 'block';
 }
 } else {
 novaquestao = questao - 1;
 document.getElementById('questao' + questao.toString()).style.display = 'none';
 document.getElementById('questao' + questao.toString()).style.height = '0px';
 document.getElementById('questao' + novaquestao.toString()).style.display = 'block';
 }
 }
 
 function validaConfirmacao() {
 var senha = document.getElementById('senhaCadastro').value;
 var confirmacao = document.getElementById('confirmacao').value;
 
 if (senha != confirmacao) {
 alert("A confirmação não confere com a senha informada!");
 document.getElementById('senhaCadastro').focus();
 }
 }
 
 function exigeConsideracoes(questao) {
 if (document.getElementById(questao + 'consideracoes').value == '' &&
 (document.getElementById(questao + 'resposta1').checked || document.getElementById(questao + 'resposta2').checked
 || document.getElementById(questao + 'resposta3').checked || document.getElementById(questao + 'resposta4').checked
 || document.getElementById(questao + 'resposta5').checked || document.getElementById(questao + 'resposta6').checked)) {
 return true;
 } else {
 return false;
 }
 }
 
 function respostaBranco(questao) {
 if (document.getElementById(questao + 'resposta1').checked || document.getElementById(questao + 'resposta2').checked
 || document.getElementById(questao + 'resposta3').checked || document.getElementById(questao + 'resposta4').checked
 || document.getElementById(questao + 'resposta5').checked || document.getElementById(questao + 'resposta6').checked
 || document.getElementById(questao + 'resposta7').checked || document.getElementById(questao + 'resposta8').checked
 || document.getElementById(questao + 'resposta9').checked || document.getElementById(questao + 'resposta10').checked
 || document.getElementById(questao + 'respostansa').checked) {
 return false;
 } else {
 return true;
 }
 }*/

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

/*function menuPrincipal(menu) {


    
     if (menu == 'menuRelatorios') {
     document.getElementById('menuRelatorios').className = 'active';
     document.getElementById('menuCriaRelatorios').className = 'inactive';
     document.getElementById('utilitarios').className = 'inactive';
     }
     if (menu == 'menuCriaRelatorios') {
     document.getElementById('menuCriaRelatorios').className = 'active';
     document.getElementById('menuRelatorios').className = 'inactive';
     document.getElementById('utilitarios').className = 'inactive';
     }
     if (menu == 'utilitarios') {
     document.getElementById('utilitarios').className = 'active';
     document.getElementById('menuRelatorios').className = 'inactive';
     document.getElementById('menuCriaRelatorios').className = 'inactive';
     }
     
     if(document.getElementById('utilitarios').style.display == 'block'){
     escondeMenu('utilitarios');
     }
     if (menu == 'menuRelatorios') {
     document.getElementById('menuRelatorios').className = 'active';
     document.getElementById('listaRelatorios').style.display = 'block';
     document.getElementById('menuCriaRelatorios').className = 'inactive';
     document.getElementById('criacaoRelatorios').style.display = 'none';
     } else {
     document.getElementById('menuCriaRelatorios').className = 'active';
     document.getElementById('criacaoRelatorios').style.display = 'block';
     document.getElementById('menuRelatorios').className = 'inactive';
     document.getElementById('listaRelatorios').style.display = 'none';
     }
}*/