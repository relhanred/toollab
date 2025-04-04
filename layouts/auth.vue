<script setup>
import LogoText from "~/components/Icons/LogoText.vue";
import Home from "~/components/Icons/Home.vue";
import Setting from "~/components/Icons/Setting.vue";
import Notification from "~/components/Icons/Notification.vue";
import Cursus from "~/components/Icons/Cursus.vue";
import NavLink from "~/components/navigation/NavLink.vue";
import FamilyTLB from "~/components/Icons/Family-TLB.vue";
import UserDropdown from "~/components/UserDropdown.vue";
import { ref, computed, onMounted } from 'vue';

const user = ref(null);
const initials = computed(() => {
  if (!user.value) return 'NA';

  const firstName = user.value.first_name || '';
  const lastName = user.value.last_name || '';

  return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
});

onMounted(() => {
  if (process.client) {
    const userJson = localStorage.getItem('auth.user');
    if (userJson) {
      try {
        user.value = JSON.parse(userJson);
      } catch (e) {
        console.error('Erreur lors de la récupération des données utilisateur', e);
      }
    }
  }
});
</script>

<template>
  <div class="flex bg-gray-blue min-h-screen antialiased">
    <aside class="flex flex-col bg-white w-64 min-h-full border-r font-medium font-montserrat">
      <div class="w-full flex items-center justify-center h-20">
        <LogoText class="w-44"/>
      </div>
      <nav class="inline-flex flex-col gap-y-4 mt-4">
        <NavLink to="/" :icon="Home" text="Accueil"/>
        <NavLink to="/cursus" :icon="Cursus" text="Cursus"/>
        <NavLink to="/family" :icon="FamilyTLB" text="Familles"/>
      </nav>
    </aside>
    <div class="flex flex-col flex-1">
      <header class="flex items-center bg-white h-20 border-b font-montserrat">
        <div class="flex items-center justify-between w-full px-10 py-4">
          <h1 class="text-3xl font-semibold text-default leading-8">
            {{ $route.meta.layoutData?.title || 'Dashboard' }}
          </h1>

          <div class="flex items-center gap-x-6">
            <div class="inline-flex items-center justify-center rounded-full bg-gray-light p-2.5">
              <NuxtLink to="/settings">
                <Setting class="size-6 text-primary"/>
              </NuxtLink>
            </div>

            <div class="inline-flex items-center justify-center rounded-full bg-gray-light p-2.5">
              <Notification class="size-6 text-red-500"/>
            </div>

            <UserDropdown :initials="initials" />
          </div>
        </div>
      </header>
      <main>
        <slot/>
      </main>
    </div>
  </div>
</template>