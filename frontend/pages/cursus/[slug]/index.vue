<script setup>
import { ref, onMounted } from "vue";
import BreadCrumb from "~/components/navigation/BreadCrumb.vue";
import SelectGenre from "~/components/form/SelectGenre.vue";
import SaveButton from "~/components/form/SaveButton.vue";
import CancelButton from "~/components/form/CancelButton.vue";
import { useRoute } from '#imports';

const route = useRoute();
const isLoading = ref(true);
const error = ref(null);

const cursus = ref({
  name: route.params.slug ? route.params.slug.charAt(0).toUpperCase() + route.params.slug.slice(1) : '',
  levels: [
    { id: 1, name: '1ère année', classes: [] },
    { id: 2, name: '2ème année', classes: [] },
    { id: 3, name: '3ème année', classes: [] }
  ],
  progression: 'levels'
});

const classes = ref([
  {
    id: 1,
    name: '1A',
    levelId: 1,
    gender: 'hommes',
    color: '#93C5FD',
    size: 30
  },
  {
    id: 2,
    name: '1B',
    levelId: 1,
    gender: 'femmes',
    color: '#FDA4AF',
    size: 25
  },
  {
    id: 3,
    name: '2A',
    levelId: 2,
    gender: 'enfants',
    color: '#FCD34D',
    size: 20
  }
]);

const newClass = ref({
  name: '',
  gender: '',
  size: ''
});

const genderColors = {
  'hommes': '#93C5FD',
  'femmes': '#FDA4AF',
  'enfants': '#FCD34D',
  'mixte': '#86EFAC'
};

onMounted(() => {
  setTimeout(() => {
    cursus.value.levels.forEach(level => {
      level.classes = classes.value.filter(c => c.levelId === level.id);
    });
    isLoading.value = false;
  }, 500);
});

const handleSaveClass = () => {
  if (!newClass.value.name || !newClass.value.gender || !newClass.value.size) {
    error.value = 'Tous les champs sont requis';
    return;
  }

  const newId = Math.max(...classes.value.map(c => c.id)) + 1;
  const newClassObj = {
    id: newId,
    name: newClass.value.name,
    levelId: 1,
    gender: newClass.value.gender,
    color: genderColors[newClass.value.gender] || '#86EFAC',
    size: parseInt(newClass.value.size)
  };

  classes.value.push(newClassObj);
  cursus.value.levels[0].classes.push(newClassObj);

  newClass.value = {
    name: '',
    gender: '',
    size: ''
  };

  error.value = null;
};

const handleCancel = () => {
  newClass.value = {
    name: '',
    gender: '',
    size: ''
  };
  error.value = null;
};

definePageMeta({
  layout: 'auth',
  layoutData: {
    title: 'Cursus',
  }
});
</script>

<template>
  <div class="container mx-auto px-10 pt-3">
    <BreadCrumb/>

    <div v-if="isLoading" class="flex justify-center py-10">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-default"></div>
    </div>

    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
      {{ error }}
    </div>

    <div v-else class="flex flex-col gap-y-6">
      <div v-for="level in cursus.levels" :key="level.id" class="flex flex-col gap-y-4">
        <div class="w-full border rounded-xl bg-[#d9d9D9] my-2"></div>
        <div class="text-default font-bold text-2xl font-montserrat text-center">{{ level.name }}</div>

        <div class="grid grid-cols-6 gap-4 py-4">
          <div v-if="level.classes.length === 0" class="col-span-6 text-center text-gray-500 py-4">
            Aucune classe pour ce niveau
          </div>

          <div v-for="classe in level.classes" :key="classe.id"
               class="bg-white rounded-3xl border-t-4 border-x border-b shadow-md p-5 flex flex-col justify-center items-center font-montserrat"
               :class="`border-t-[${classe.color}]`">
            <h2 :class="`text-[${classe.color}] text-2xl font-bold`">{{ classe.name }}</h2>
            <p :class="`text-[${classe.color}] font-medium text-xs`">{{ classe.size }} places</p>
          </div>
        </div>
      </div>

      <div class="w-full border rounded-xl bg-[#d9d9D9] my-2"></div>
      <div class="text-default font-bold text-2xl font-montserrat text-center mb-4">Nouvelle classe</div>

      <div class="grid grid-cols-3 gap-x-10 p-6 bg-white rounded-lg shadow">
        <input type="text"
               v-model="newClass.name"
               class="bg-white placeholder:text-placeholder border rounded-lg focus:outline-default border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default"
               placeholder="Nom de la classe"/>
        <SelectGenre
            v-model="newClass.gender"
            placeholder="Genre"
        />
        <input type="number"
               v-model="newClass.size"
               class="bg-white placeholder:text-placeholder border rounded-lg focus:outline-default border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default"
               placeholder="Effectif maximum"/>
      </div>

      <div class="flex items-center justify-center mt-6 mb-10">
        <SaveButton @click="handleSaveClass" class="mr-8">
          Enregistrer
        </SaveButton>
        <CancelButton @click="handleCancel">
          Annuler
        </CancelButton>
      </div>
    </div>
  </div>
</template>