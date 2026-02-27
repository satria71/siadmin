import { onMounted, onBeforeUnmount } from 'vue'
import $ from 'jquery'

import 'datatables.net-bs5'
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css'

import 'datatables.net-buttons-bs5'
import 'datatables.net-buttons/js/buttons.html5'
import 'datatables.net-buttons/js/buttons.print'
import 'datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css'

import JSZip from 'jszip'
import pdfMake from 'pdfmake/build/pdfmake'
import pdfFonts from 'pdfmake/build/vfs_fonts'

window.JSZip = JSZip
pdfMake.vfs = pdfFonts.vfs

export function useDataTable(tableId, ajaxUrl, columns, options = {}) {
    let table = null

    onMounted(() => {
        if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
            $(`#${tableId}`).DataTable().destroy()
        }

        table = $(`#${tableId}`).DataTable({
            processing: true,
            serverSide: true,
            ajax: ajaxUrl,
            columns: columns,
            ...options
        })

        // if (options.buttons) {
        //     table.buttons().container().appendTo('#exportButtons')
        // }
    })

    onBeforeUnmount(() => {
        if (table) {
            table.destroy(true)
            table = null
        }
    })

    return { table }
}