<script src="<?php echo SERVERURL; ?>views/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/bootstrap.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/material.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/ripples.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/trumbowyg.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/es.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/jquery.dataTables.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/dataTables.select.min.js"></script>

<!-- JS de DataTables Buttons (ahora local) -->
<script src="<?php echo SERVERURL; ?>views/js/dataTables.buttons.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/jszip.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/buttons.html5.min.js"></script>
<script src="<?php echo SERVERURL; ?>views/js/buttons.print.min.js"></script>

<script>
    $('#spv-editor').trumbowyg({
        btns: [
            ['viewHTML'],
            ['undo', 'redo'], // Only supported in Blink browsers
            ['formatting'],
            ['strong', 'em', 'del'],
            ['superscript', 'subscript'],
            ['link'],
            ['insertImage'],
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
            ['fullscreen']
        ],
        autogrow: true,
        lang: 'es'
    });
</script>
<script src="<?php echo SERVERURL; ?>views/js/main.js"></script>
<script>
    $.material.init();
</script>
<script>
$(document).ready(function () {
    var table = $('#tabla-global').DataTable({
        "language": {
            "url": "<?php echo SERVERURL; ?>views/lang/datatables/es-ES.json"
        },
        "dom": 'lBfrtip',
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "buttons": [
            {
                extend: 'excelHtml5',
                titleAttr: 'Exportar a Excel',
                text: '<i class="zmdi zmdi-file-text" style="color:white"></i>',
                className: 'btn btn-success'
            },
            {
                extend: 'print',
                titleAttr: 'Imprimir',
                text: '<i class="zmdi zmdi-print" style="color:white"></i>',
                className: 'btn btn-info'
            }
        ]
    });

    new $.fn.dataTable.ColumnControl(table, {
        title: 'Mostrar/Ocultar Columnas',
        text: 'Columnas',
        className: 'btn btn-primary'
    });
});
    window.addEventListener("load", function () {
        const loader = document.getElementById("global-loader");
        loader.style.opacity = "0";
        loader.style.pointerEvents = "none";

        // Opcional: remover del DOM después de la transición
        setTimeout(() => {
            loader.style.display = "none";
        }, 500);
    });
</script>