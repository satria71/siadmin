
<script setup>
// import Layout from '../../Layout.vue'
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

defineProps({ user: Object })

const form = useForm({
  nik: '',
  nama: '',
  bagian: '',
  password: '',
  remember: false
})
  

const submit = () => {
  form.post('/register')
}
</script>

<template>
    <Head title="Register" />
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <!-- BEGIN NAVBAR LOGO -->
           <!-- <a href="." aria-label="Tabler" class="navbar-brand navbar-brand-autodark">
            <svg xmlns="http://www.w3.org/2000/svg" width="110" height="32" viewBox="0 0 232 68" class="navbar-brand-image">
              <path
                d="M64.6 16.2C63 9.9 58.1 5 51.8 3.4 40 1.5 28 1.5 16.2 3.4 9.9 5 5 9.9 3.4 16.2 1.5 28 1.5 40 3.4 51.8 5 58.1 9.9 63 16.2 64.6c11.8 1.9 23.8 1.9 35.6 0C58.1 63 63 58.1 64.6 51.8c1.9-11.8 1.9-23.8 0-35.6zM33.3 36.3c-2.8 4.4-6.6 8.2-11.1 11-1.5.9-3.3.9-4.8.1s-2.4-2.3-2.5-4c0-1.7.9-3.3 2.4-4.1 2.3-1.4 4.4-3.2 6.1-5.3-1.8-2.1-3.8-3.8-6.1-5.3-2.3-1.3-3-4.2-1.7-6.4s4.3-2.9 6.5-1.6c4.5 2.8 8.2 6.5 11.1 10.9 1 1.4 1 3.3.1 4.7zM49.2 46H37.8c-2.1 0-3.8-1-3.8-3s1.7-3 3.8-3h11.4c2.1 0 3.8 1 3.8 3s-1.7 3-3.8 3z"
                fill="#066fd1"
                style="fill: var(--tblr-primary, #066fd1)"
              />
            </svg>
            <h1>SIADMIN</h1>
          </a> -->
          <h1>SIADMIN</h1>
          
          <!-- END NAVBAR LOGO -->
        </div>
        <form @submit.prevent="submit">
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Buat Akun SIADMIN</h2>
                    <div class="mb-3">
                    <label class="form-label">NIK</label>
                    <input type="text" v-model="form.nik" class="form-control" placeholder="2015496800" />
                    </div>
                    <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" v-model="form.nama" class="form-control" placeholder="Masukkan nama" />
                    </div>
                    <div class="mb-3">
                    <label class="form-label">Bagian</label>
                    <input type="text" v-model="form.bagian" class="form-control" placeholder="Masukkan bagian" />
                    </div>
                    <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group input-group-flat">
                        <input type="password" v-model="form.password" class="form-control" placeholder="Password" autocomplete="off" />
                        <span class="input-group-text">
                        <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip"
                            ><!-- Download SVG icon from http://tabler.io/icons/icon/eye -->
                            <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-1"
                            >
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                            </svg></a>
                        </span>
                    </div>
                    </div>
                    <div class="mb-3">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" />
                        <span class="form-check-label">Agree the <a href="./terms-of-service.html" tabindex="-1">terms and policy</a>.</span>
                    </label>
                    </div>
                    <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create new account</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="text-center text-secondary mt-3">Already have account? <Link href="login" tabindex="-1">Sign in</Link></div>
      </div>
    </div>
</template>



