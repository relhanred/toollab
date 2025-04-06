<script setup>
import BreadCrumb from "~/components/navigation/BreadCrumb.vue";
import Dots from "~/components/Icons/Dots.vue";
import { useRoute }  from '#imports'
import SelectGenre from "~/components/form/SelectGenre.vue";

const route = useRoute()

definePageMeta({
  layout: 'auth',
  layoutData: {
    title: 'Cursus',
  }
})


const selectedGenre = ref('')


</script>

<template>
  <div class="container mx-auto px-10 pt-3">
    <BreadCrumb/>

    <div class="grid grid-cols-5 gap-x-6 gap-y-2 pb-4">
      <NuxtLink
                  class="inline-flex flex-col items-start justify-start bg-white border text-default cursor-pointer hover:opacity-80 font-montserrat font-bold text-2xl p-4 rounded-2xl">
        <Dots  class="ml-auto size-5 cursor-pointer transition-colors hover:opacity-80"/>
        <span>1er année</span>
        <span class="mt-3 text-sm font-normal mb-4">Aucune classe</span>
      </NuxtLink>
      <NuxtLink class="inline-flex flex-col items-start justify-start bg-white border text-default cursor-pointer hover:opacity-80 font-montserrat font-bold text-2xl p-4 rounded-2xl">
        <Dots  class="ml-auto size-5 cursor-pointer transition-colors hover:opacity-80"/>
        <span>2eme année</span>
        <span class="mt-3 text-sm font-normal mb-4">Aucune classe</span>
      </NuxtLink>
    </div>

    <div class="w-full border rounded-xl bg-[#d9d9D9]"></div>

    <div class="text-default font-bold text-2xl font-montserrat inline-flex items-center justify-center w-full mt-4">1er année</div>

    <div class="grid grid-cols-6 gap-x-6 gap-y-2 py-4">


      <div class="bg-white rounded-3xl border-t-4 border-x border-b border-t-[#93C5FD] shadow-md p-5 flex flex-col justify-center items-center font-montserrat">
        <h2 class="text-[#93C5FD] text-2xl font-bold">2eA</h2>
        <p class="text-[#93C5FD] font-medium text-xs">30 places</p>
      </div>


      <div class="bg-white rounded-3xl border-t-4 border-x border-b border-t-[#FDA4AF] shadow-md p-5 flex flex-col justify-center items-center font-montserrat">
        <h2 class="text-[#F43F5E] text-2xl font-bold">2eA</h2>
        <p class="text-[#F43F5E] font-medium text-xs">30 places</p>
      </div>

      <div class="bg-white rounded-3xl border-t-4 border-x border-b border-t-[#FDE68A] shadow-md p-5 flex flex-col justify-center items-center font-montserrat">
        <h2 class="text-[#F59E0B] text-2xl font-bold">2eB</h2>
        <p class="text-[#F59E0B] font-medium text-xs">30 places</p>
      </div>

      <div class="bg-white rounded-3xl border-t-4 border-x border-b border-t-[#BBF7D0] shadow-md p-5 flex flex-col justify-center items-center font-montserrat">
        <h2 class="text-[#22C55E] text-2xl font-bold">2eA</h2>
        <p class="text-[#22C55E] font-medium text-xs">30 places</p>
      </div>
    </div>


    <div class="w-full border rounded-xl bg-[#d9d9D9]"></div>

    <div class="text-default font-bold text-2xl font-montserrat inline-flex items-center justify-center w-full mt-4">Nouvelle classe</div>

    <div class="grid grid-cols-3 gap-x-10 p-10">
      <input type="text"
             class="bg-white placeholder:text-placeholder border rounded-lg focus:outline-default border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default"
             placeholder="Nom de la classe"/>
      <SelectGenre    v-model="selectedGenre" placeholder="Genre"/>
      <input type="text"
             class="bg-white placeholder:text-placeholder border rounded-lg focus:outline-default border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default"
             placeholder="Effectif maximum"/>
    </div>

    <div class="flex items-center justify-center mt-8">
      <button class="bg-default py-3 px-6 font-nunito font-semibold text-white hover:opacity-80 rounded-lg mr-8 transition-colors active:bg-default/80">
        Enregistrer
      </button>
      <button class=" py-3 px-6 font-nunito font-semibold text-default border hover:opacity-70 bg-white border-placeholder rounded-lg">
        Annuler
      </button>
    </div>

  </div>
</template>