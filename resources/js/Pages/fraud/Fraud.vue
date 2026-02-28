<script setup>
import Layout from '../../Layout.vue'
import { useDataTable } from '@/composables/useDataTable'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'

const showModal = () => {
    const modal = new Modal(document.getElementById('modal-report'))
    modal.show()
}

const user = computed(() => usePage().props.auth.user)

defineProps({ user: Object })

useDataTable(
    'fraudTable',
    '/fraud/data',

        [
            {
                data: null,
                name: 'no',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },  
            { data: 'tanggal', name: 'tanggal' ,
                render: function (data, type) {
                    if (type === 'display') {
                        const date = new Date(data);
                        return date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        });
                    }
                    return data; // untuk sorting tetap pakai format asli
                }
            },
            { data: 'nik', name: 'nik' },
            { data: 'fraud', name: 'fraud' },
            {
                data: 'id',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-warning btn-sm edit-btn" data-id="${data}">
                            <i class="fa-solid fa-pen-to-square"> </i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${data}">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    `;
                }
            },
        ],
    {
        dom:
            "<'row mb-3 align-items-center'<'col-md-4'l><'col-md-8 d-flex justify-content-end align-items-center gap-2'fB>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3 align-items-center'<'col-md-6'i><'col-md-6 d-flex justify-content-end'p>>",

        language: {
            paginate: {
                previous: "Previous",
                next: "Next"
            }
        },

        columnDefs: [
            {
                targets: "_all",
                className: "text-center"
            }
        ],

        buttons: [
            {
                extend: 'excel',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: 'Export PDF',
                className: 'btn btn-danger btn-sm'
            }
        ]
    },
)

</script>

<template>
    <Layout>
        <Head title="Fraud" />
        <!-- BEGIN PAGE HEADER -->
        <div class="page-header d-print-none" aria-label="Page header">
            <div class="container-fluid">
                <div class="row g-2 align-items-center">
                    <div class="col">
                    <!-- Page pre-title -->
                    <!-- <div class="page-pretitle">Overview</div> -->
                    <h2 class="page-title">Fraud</h2>
                    </div>
                    <!-- Page title actions -->
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="#" class="btn btn-primary btn-5 d-none d-sm-inline-block"
                            @click.prevent="showModal">
                            <i class="fa-solid fa-plus"> </i> Buat Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE HEADER -->
        
        <div class="page-body">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-table">
                        <div class="card-header">
                            <div class="col">
                          <h3 class="card-title mb-0">Fraud Admin</h3>
                          <p class="text-secondary m-0">Monitoring data fraud admin</p>
                        </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="fraudTable" class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Tanggal</th>
                                            <th>NIK</th>
                                            <th>Fraud</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BEGIN MODAL -->
        <div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Fraud Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="example-text-input" placeholder="Your report name">
                </div>
                <label class="form-label">Report type</label>
                <div class="form-selectgroup-boxes row mb-3">
                    <div class="col-lg-6">
                    <label class="form-selectgroup-item">
                        <input type="radio" name="report-type" value="1" class="form-selectgroup-input" checked="">
                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                        <span class="me-3">
                            <span class="form-selectgroup-check"></span>
                        </span>
                        <span class="form-selectgroup-label-content">
                            <span class="form-selectgroup-title strong mb-1">Simple</span>
                            <span class="d-block text-secondary">Provide only basic data needed for the report</span>
                        </span>
                        </span>
                    </label>
                    </div>
                    <div class="col-lg-6">
                    <label class="form-selectgroup-item">
                        <input type="radio" name="report-type" value="1" class="form-selectgroup-input">
                        <span class="form-selectgroup-label d-flex align-items-center p-3">
                        <span class="me-3">
                            <span class="form-selectgroup-check"></span>
                        </span>
                        <span class="form-selectgroup-label-content">
                            <span class="form-selectgroup-title strong mb-1">Advanced</span>
                            <span class="d-block text-secondary">Insert charts and additional advanced analyses to be inserted in the report</span>
                        </span>
                        </span>
                    </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                    <div class="mb-3">
                        <label class="form-label">Report url</label>
                        <div class="input-group input-group-flat">
                        <span class="input-group-text"> https://tabler.io/reports/ </span>
                        <input type="text" class="form-control ps-0" value="report-01" autocomplete="off">
                        </div>
                    </div>
                    </div>
                    <div class="col-lg-4">
                    <div class="mb-3">
                        <label class="form-label">Visibility</label>
                        <select class="form-select">
                        <option value="1" selected="">Private</option>
                        <option value="2">Public</option>
                        <option value="3">Hidden</option>
                        </select>
                    </div>
                    </div>
                </div>
                </div>
                <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Client name</label>
                        <input type="text" class="form-control">
                    </div>
                    </div>
                    <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label">Reporting period</label>
                        <input type="date" class="form-control">
                    </div>
                    </div>
                    <div class="col-lg-12">
                    <div>
                        <label class="form-label">Additional information</label>
                        <textarea class="form-control" rows="3"></textarea>
                    </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                <a href="#" class="btn btn-link link-secondary btn-3" data-bs-dismiss="modal"> Cancel </a>
                <a href="#" class="btn btn-primary btn-5 ms-auto" data-bs-dismiss="modal">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-2">
                    <path d="M12 5l0 14"></path>
                    <path d="M5 12l14 0"></path>
                    </svg>
                    Create new report
                </a>
                </div>
            </div>
            </div>
        </div>
        <!-- END MODAL -->
    </Layout>
</template>