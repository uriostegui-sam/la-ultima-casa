<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/shared/stores/AuthStore'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'
import Logo from '@/visitors/components/Logo.vue'
import AuthService from '@/shared/services/DataLayers/AuthService'
import type { PasswordResetPayload } from '@/shared/Interfaces/User'

const { t } = useI18n()
const router = useRouter()
const credentials = ref({
  email: '',
  password: '',
  rememberMe: false,
})
const authStore = useAuthStore()
const loading = ref(false)
const error = ref('')
const isPasswordForgotten = ref(false)
const currentReset = ref({
  token: '',
  new_password: '',
  new_password_confirmation: '',
})

function passwordForgotten() {
  isPasswordForgotten.value = !isPasswordForgotten.value
}

async function handleResetPassword() {
  try {
    const payload: PasswordResetPayload = {
      token: currentReset.value.token,
      new_password: currentReset.value.new_password,
      new_password_confirmation: currentReset.value.new_password_confirmation,
    }
    loading.value = true
    error.value = ''

    await AuthService.resetPasswordWToken(payload)
    error.value = t('passwordResetEmailSent')
  } catch (err: any) {
    error.value = err.message || t('errorSendingResetEmail')
  } finally {
    loading.value = false
  }
}
async function handleLogin() {
  try {
    loading.value = true
    error.value = ''

    const response = await authStore.login(credentials.value)
    // Redirect based on role
    if (response.data.user.role === 'admin') {
      router.push('/admin')
    } else {
      router.push('/admin/artists/edit/' + response.data.user.artist_id)
    }
  } catch (err: any) {
    error.value = err.message || 'Invalid credentials'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
    <div class="flex flex-col items-center justify-center">
      <div class="bg-(--color-light-salmon) rounded-4xl">
        <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20">
          <div class="text-center mb-8 flex flex-col items-center">
            <Logo class="pb-8" />
            <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">
              {{ capitalizeFirstLetter(t('welcomeTo')) }} La Última Casa Admin
            </div>
            <span v-if="!isPasswordForgotten" class="text-muted-color font-medium">{{
              capitalizeFirstLetter(t('signInCont'))
            }}</span>
            <span v-if="isPasswordForgotten" class="text-muted-color font-medium">{{
              capitalizeFirstLetter(t('askToken'))
            }}</span>
          </div>

          <!-- Error message display -->
          <div v-if="error" class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ error }}
          </div>

          <div>
            
            <div v-if="!isPasswordForgotten">
              <label
                for="email1"
                class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2"
                >{{ capitalizeFirstLetter(t('email')) }}</label
              >
              <InputText
                id="email1"
                type="email"
                :placeholder="capitalizeFirstLetter(t('email'))"
                class="w-full mb-8"
                v-model="credentials.email"
                :disabled="loading"
              />
              <label
                for="password1"
                class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2"
                >{{ capitalizeFirstLetter(t('password')) }}</label
              >
              <Password
                id="password1"
                v-model="credentials.password"
                :placeholder="capitalizeFirstLetter(t('password'))"
                :toggleMask="true"
                class="mb-8"
                fluid
                :feedback="false"
                :disabled="loading"
              ></Password>
            </div>
            <div v-else>
              <label class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">{{
                capitalizeFirstLetter('token')
              }}</label>
              
              <InputText
                v-model="currentReset.token"
                :placeholder="capitalizeFirstLetter(t('writeToken'))"
                class="w-full mb-8"
              />

              <label class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">{{
                capitalizeFirstLetter(t('newPassword'))
              }}</label>
              
              <Password
                v-model="currentReset.new_password"
                :placeholder="capitalizeFirstLetter(t('writeNewPassword'))"
                class="w-full mb-8"
                :style="{ width: '100%' }"
                :inputStyle="{ width: '100%' }"
                :weakLabel="capitalizeFirstLetter(t('weakLabel'))"
                :mediumLabel="capitalizeFirstLetter(t('mediumLabel'))"
                :strongLabel="capitalizeFirstLetter(t('strongLabel'))"
                :toggleMask="true"
              />

              <label class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">{{
                capitalizeFirstLetter(t('newPasswordAgain'))
              }}</label>
              <Password
                v-model="currentReset.new_password_confirmation"
                :placeholder="capitalizeFirstLetter(t('writeNewPasswordAgain'))"
                class="w-full mb-8"
                :style="{ width: '100%' }"
                :inputStyle="{ width: '100%' }"
                :toggleMask="true"
              />
            </div>
            <div v-if="!isPasswordForgotten" class="flex items-center justify-between mt-2 mb-8 gap-8">
              <div class="flex items-center">
                <Checkbox
                  v-model="credentials.rememberMe"
                  id="rememberme1"
                  binary
                  class="mr-2"
                  :disabled="loading"
                ></Checkbox>
                <label for="rememberme1">{{ capitalizeFirstLetter(t('rememberMe')) }}</label>
              </div>
              <span
                class="font-medium no-underline ml-2 text-right cursor-pointer text-primary"
                @click="passwordForgotten"
                >{{ capitalizeFirstLetter(t('forgotPassword')) }}</span
              >
            </div>
            <div v-else class="flex items-center justify-between mt-2 mb-8 gap-8">
              <span
                class="font-medium no-underline ml-2 text-right cursor-pointer text-primary"
                @click="passwordForgotten"
                >{{ capitalizeFirstLetter(t('iRememberPassword')) }}</span
              >
            </div>

            <div v-if="!isPasswordForgotten">
              <Button
                :label="capitalizeFirstLetter(t('signIn'))"
                severity="warn"
                class="w-full bg-salmon"
                @click="handleLogin"
                :loading="loading"
                :disabled="loading"
              />
            </div>
            <div v-else>
              <Button
                :label="capitalizeFirstLetter(t('resetPassword'))"
                severity="warn"
                class="w-full bg-salmon"
                @click="handleResetPassword"
                :loading="loading"
                :disabled="loading"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
