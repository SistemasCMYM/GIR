/**
 * DataTable Configuration for Empleados Module
 * Silva Theme Style - GIR365
 */

$(document).ready(function () {
    // Inicializar DataTable con configuración Silva Theme
    var table = $('#empleadosTable').DataTable({
        responsive: true,
        pageLength: 50,
        lengthMenu: [[50, 100, 500, 1000, -1], [50, 100, 500, 1000, "Todos"]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
            search: "_INPUT_",
            searchPlaceholder: "Buscar en todos los campos...",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ empleados",
            infoEmpty: "No hay empleados para mostrar",
            infoFiltered: "(filtrado de _MAX_ empleados totales)",
            zeroRecords: "No se encontraron empleados que coincidan",
            emptyTable: "No hay empleados registrados en la base de datos",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            }
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        order: [[0, 'asc']],
        columnDefs: [
            { responsivePriority: 1, targets: 0 },  // DNI
            { responsivePriority: 2, targets: 1 },  // Primer Nombre
            { responsivePriority: 3, targets: -1 }, // Acciones
            { orderable: false, targets: -1 },      // No ordenar acciones
            { className: 'text-center', targets: -1 }
        ],
        initComplete: function () {
            // Agregar filtros por columna en el header
            this.api().columns([5, 8, 9, 10, 11, 12, 13]).every(function () {
                var column = this;
                var columnIndex = column.index();

                // Crear select para filtros
                var select = $('<select class="form-select form-select-sm mt-1"><option value="">Todos</option></select>')
                    .appendTo($(column.header()))
                    .on('change', function () {
                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    })
                    .on('click', function (e) {
                        e.stopPropagation();
                    });

                // Obtener valores únicos
                column.data().unique().sort().each(function (d, j) {
                    // Extraer texto de badges/spans si existen
                    var text = $('<div>').html(d).text().trim();
                    if (text && text !== '-' && text !== 'N/A' && text !== 'Sin área' &&
                        text !== 'Sin proceso' && text !== 'Sin sede' && text !== 'Sin cargo' &&
                        text !== 'Sin email') {
                        select.append('<option value="' + text + '">' + text + '</option>');
                    }
                });
            });
        },
        drawCallback: function () {
            // Aplicar estilos después de cada redibujado
            $('.dataTables_paginate .pagination').addClass('pagination-sm');
        }
    });

    // Exportar a Excel usando XLSX
    $('#exportExcel').on('click', function () {
        // Obtener datos filtrados
        var data = [];
        var headers = [];

        // Headers
        $('#empleadosTable thead th').each(function () {
            if ($(this).text().trim() !== 'Acciones') {
                headers.push($(this).text().trim());
            }
        });
        data.push(headers);

        // Data
        table.rows({ search: 'applied' }).every(function () {
            var rowData = [];
            var row = this.data();
            for (var i = 0; i < row.length - 1; i++) {
                var cellText = $('<div>').html(row[i]).text().trim();
                rowData.push(cellText);
            }
            data.push(rowData);
        });

        // Crear workbook
        var ws = XLSX.utils.aoa_to_sheet(data);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Empleados");

        // Descargar
        var fecha = new Date().toISOString().slice(0, 10);
        XLSX.writeFile(wb, 'empleados_' + fecha + '.xlsx');
    });

    // Mejorar apariencia del buscador
    $('.dataTables_filter input')
        .addClass('form-control form-control-sm')
        .attr('placeholder', 'Buscar en todos los campos...');

    $('.dataTables_length select').addClass('form-select form-select-sm');
});

// Funciones para acciones
function verEmpleado(id) {
    window.location.href = '/empresa/empleados/' + id;
}

function editarEmpleado(id) {
    window.location.href = '/empresa/empleados/' + id + '/edit';
}

function eliminarEmpleado(id, nombre) {
    Swal.fire({
        title: '¿Está seguro?',
        text: '¿Desea eliminar al empleado ' + nombre + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/empresa/empleados/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire(
                        'Eliminado',
                        'El empleado ha sido eliminado correctamente',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function (error) {
                    Swal.fire(
                        'Error',
                        'Hubo un problema al eliminar el empleado',
                        'error'
                    );
                }
            });
        }
    });
}
