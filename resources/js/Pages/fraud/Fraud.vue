<script setup>
import Layout from '../../Layout.vue'
import { useDataTable } from '@/composables/useDataTable'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Modal } from 'bootstrap'

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
        { data: 'tanggal', name: 'tanggal' },
        { data: 'nik', name: 'nik' },
        { data: 'fraud', name: 'fraud' },
    ],
    {
        dom:
            "<'row mb-3 align-items-center'<'col-md-4'l><'col-md-8 d-flex justify-content-end align-items-center gap-2'fB>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3 align-items-center'<'col-md-6'i><'col-md-6 d-flex justify-content-end'p>>",

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
                                Buat Baru
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
                        <!-- <div class="card-header">
                            <div class="row w-full">
                                <div class="col">
                                    <h3 class="card-title mb-0">Fraud</h3>
                                    <p class="text-secondary m-0">Table description.</p>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="fraudTable" class="table table-vcenter table-striped">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Tanggal</th>
                                            <th>NIK</th>
                                            <th>Fraud</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>