
<script setup>
import PlusLight from "~/components/Icons/PlusLight.vue";
import ResponsableTLB from "~/components/Icons/Responsable-TLB.vue";
import {ref} from "vue";
import AddResponsableModal from "~/components/modals/AddResponsableModal.vue";

definePageMeta({
  layout: 'auth',
  layoutData: {
    title: 'Famille'
  }
})

const showAddResponsableModal = ref(false);

const handleAddResponsable = async (newResponsable) => {
  try {
    if (newResponsable && newResponsable.family_id) {
      navigateTo(`/family/${newResponsable.family_id}`);
    }
  } catch (error) {
    console.error('Erreur lors de la redirection vers la famille:', error);
  }
}

const responsables = ref([
  {
    id: 1,
    nom: 'Foulane Mohammed',
    nombreEleves: 4,
    status: 'paid',
    dateInscription: '28 Jan, 12.30 AM'
  },
  {
    id: 2,
    nom: 'Bilal Mohammed',
    nombreEleves: 2,
    status: 'pending',
    dateInscription: '25 Jan, 10.40 PM'
  },
  {
    id: 3,
    nom: 'Fatima Mohammed',
    nombreEleves: 3,
    status: 'incomplete',
    dateInscription: '20 Jan, 10.40 PM'
  },
  {
    id: 4,
    nom: 'Khadija Mohammed',
    nombreEleves: 1,
    status: 'exempted',
    dateInscription: '15 Jan, 03.29 PM'
  }
]);
</script>

<template>
  <div class="flex flex-col gap-y-6 w-full pt-10 px-10 font-montserrat">
    <AddResponsableModal
        :is-open="showAddResponsableModal"
        @close="showAddResponsableModal = false"
        @save="handleAddResponsable"
    />

    <button
        @click="showAddResponsableModal = true"
        class="bg-default text-white px-5 py-2 w-fit rounded-lg hover:opacity-90 inline-flex items-center justify-between gap-x-2 ml-auto">
      <PlusLight class="size-4"/>
      <span>Créer une famille</span>
    </button>

    <div class="grid grid-rows-5 w-full py-4 px-10 bg-white rounded-2xl divide-y border divide-[#E6EFF5]">
      <div class="grid grid-cols-12 font-bold font-montserrat">
        <div class="col-span-5 inline-flex items-center justify-start pl-10">Nom du responsable</div>
        <div class="col-span-2 inline-flex items-center justify-start">Nombre d'élèves</div>
        <div class="col-span-2 inline-flex items-center justify-start">Réglement</div>
        <div class="col-span-3 inline-flex items-center justify-start">Date d'inscription</div>
      </div>

      <NuxtLink
          v-for="responsable in responsables"
          :key="responsable.id"
          :to="`/family/${responsable.id}`"
          class="grid grid-cols-12 font-nunito py-4 hover:bg-gray-50 transition-colors cursor-pointer">
        <div class="col-span-5 inline-flex items-center justify-start gap-x-4 pl-1">
          <ResponsableTLB />
          <span>{{ responsable.nom }}</span>
        </div>
        <div class="col-span-2 inline-flex items-center justify-start">
          {{ responsable.nombreEleves }}
        </div>
        <div class="col-span-2 inline-flex items-center justify-start">
          <Tag :status="responsable.status" />
        </div>
        <div class="col-span-3 inline-flex items-center justify-start">
          {{ responsable.dateInscription }}
        </div>
      </NuxtLink>
    </div>
  </div>
</template>