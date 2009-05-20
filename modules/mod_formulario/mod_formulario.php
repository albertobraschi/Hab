<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

// Load the virtuemart main parse code
if( file_exists(dirname(__FILE__).'/../../components/com_virtuemart/virtuemart_parser.php' )) {
    require_once( dirname(__FILE__).'/../../components/com_virtuemart/virtuemart_parser.php' );
} else {
    require_once( dirname(__FILE__).'/../components/com_virtuemart/virtuemart_parser.php' );
}

global $VM_LANG, $vm_mainframe;

?>
<div class="formulario">
    
     <!-- Campo auto-explicativo -->
    <h3>Menu da Área de Compra</h3>
    
    <ul>
        <li><a href="#">Dúvidas</a></li>
        <li class="last"><a href="#">Histórico</a></li>
    </ul>
    <br style="clear:both" />
     <!-- Campo para texto editavel -->
    <p>
        Texto explicativo da Seção de "Compra" (Virtuemart)<br />
        Lorem ipsum dolor sit amet.
    </p>

     <!-- Campo para Data do servidor -->
    <div class="timeServer">
        <?php echo strftime("%A, %B %Y - %H:%M",time()); ?>
    </div>
     <br style="clear:both" />
    <!-- Inicio do formulario de pedido -->

    <form action="#" method="post">

         <!--Dados do cliente -->
        <table class="dadosCliente">
            <tr>
                <td>Solicitante: (Preenchido automaticamente)</td>
                <td>Cód: (Preenchido automaticamente)</td>
                <td>Contato: (Preenchido automaticamente)</td>
            </tr>
            <tr>
                <td>Cidade: (Preenchido automaticamente)</td>
                <td>Tel: (Preenchido automaticamente)</td>
                <td>Estado: (Preenchido automaticamente)</td>
            </tr>
        </table>

          <!-- Cabeçalho da tabela -->
        <table class="full" id="pedido" cellspacing="1">
            <thead>
                <tr>
                    <th scope="col" colspan="1">Item</th>
                    <th scope="col" colspan="1">Código</th>
                    <th scope="col" colspan="1">Custo Unitário(R$)</th>
                    <th scope="col" colspan="1">Quantidade</th>
                    <th scope="col" colspan="1">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                 <!--Categoria de produto -->
                <tr>
                    <th scope="row" class="sub" colspan="5">Departamento 1</th>
                </tr>
                 <!--Produtos da  respectiva Categoria -->
                <tr>
                    <th scope="col">Item 1</th>
                    <th scope="col">Cód. Item 1.1</th>
                    <th scope="col"> A </th>
                    <th scope="col"> G </th>
                    <th scope="col"> A x G </th>
                </tr>

                <!-- Soma do pedido -->
                <tr>
                    <th scope="row" class="sub" id="total" colspan="4">Total</th>
                    <th scope="row" class="sub" id="soma" colspan="1">Soma</th>
                </tr>
            <tbody>
        </table>

         <!-- Campo para texto editavel -->
        <p>
            <span>Texto editável (Observações)</span><br />
            Lorem ipsum dolor sit amet.
        </p>

         <!-- Campo para texto editavel -->
        <ol>
            <span>Texto editável (Observações)</span><br />
            <li> Lorem ipsum dolor sit amet.</li>
            <li> Lorem ipsum dolor sit amet.</li>
            <li> Lorem ipsum dolor sit amet.</li>
            <li> Lorem ipsum dolor sit amet.</li>
            <li> Lorem ipsum dolor sit amet.</li>
            <li> Lorem ipsum dolor sit amet.</li>
        </ol>

          <!-- Boolean -->
        <div class="agree">
            <input type="checkbox"/>
            <span>Aceito os termos de compra</span>
        </div>


           <!-- Encerra pedido e vai pra tela de confirmação -->
        <input type="submit" class="enviarPedido" value="Enviar Pedido">

    </form>
</div>