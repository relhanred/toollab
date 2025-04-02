<script setup>
import ToogleCursus from "~/components/form/ToogleCursus.vue";
import InputCross from "~/components/form/InputCross.vue";
import PlusLight from "~/components/Icons/PlusLight.vue";
import Dots from "~/components/Icons/Dots.vue";


definePageMeta({
  layout: 'auth',
  layoutData: {
    title: 'Cursus',
  }
})

const showCursus = ref(false);
const createdCursus = ref([
  {
    name: 'Arabe',
    levels: ['1er', '2eme'],
    progression: 'levels'
  },

]);

const currentCursus = ref({
  name: '',
  levels: [''],
  progression: 'levels'
});

const addCursus = () => {
  showCursus.value = true;
}

const addLevel = () => {
  currentCursus.value.levels.push('');
}

const deleteLevel = (index) => {
  if (index > 0) {
    currentCursus.value.levels.splice(index, 1);
  }
}

const updateLevel = (index, value) => {
  currentCursus.value.levels[index] = value;
}

const handleSave = () => {
  if (currentCursus.value.name && currentCursus.value.levels[0]) {
    const nonEmptyLevels = currentCursus.value.levels.filter(level => level.trim() !== '');
    if (nonEmptyLevels.length > 0) {
      createdCursus.value.push({
        name: currentCursus.value.name,
        levels: nonEmptyLevels,
        progression: currentCursus.value.progression
      });
      resetForm();
    }
  }
}

const resetForm = () => {
  currentCursus.value = {
    name: '',
    levels: [''],
    progression: 'levels'
  };
  showCursus.value = false;
}
</script>

<template>
  <div class="flex flex-col mx-10  h-full rounded-md">
    <div class="relative grid grid-cols-5 gap-x-6 gap-y-2 py-4 mt-12">
      <NuxtLink v-for="cursus in createdCursus" :key="cursus.id" :to="`/cursus/${cursus.name}`"
                class="inline-flex flex-col items-start justify-start bg-white text-default  border cursor-pointer hover:opacity-80 font-montserrat font-bold text-2xl  px-4 pt-4 pb-8 rounded-2xl">
        <Dots class="ml-auto size-5 cursor-pointer transition-colors hover:opacity-80"/>
        <span>{{ cursus.name }}</span>
        <span class="text-sm font-normal mt-2">{{
            cursus.levels.length
          }} {{ cursus.levels.length === 1 ? 'niveau' : 'niveaux' }}</span>
      </NuxtLink>

    </div>


    <div class="flex flex-col gap-y-4 mt-6">
      <div class="w-full border rounded-xl bg-[#d9d9D9]"></div>
      <div class="mx-auto font-montserrat text-2xl text-default font-bold mb-4">Nouveau Cursus</div>
      <div class="grid grid-cols-2 gap-x-20">
        <div class="flex flex-col mt-6 gap-y-6">
          <input type="text"
                 class="bg-white placeholder:text-placeholder border rounded-lg focus:outline-primary border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default"
                 placeholder="Nom du Cursus" v-model="currentCursus.name"/>
          <template v-for="(level, index) in currentCursus.levels" :key="index">
            <div v-if="index === 0">
              <input
                  v-model="currentCursus.levels[0]"
                  type="text"
                  class="bg-white w-full placeholder:text-placeholder border rounded-lg focus:outline-primary border-placeholder font-nunito placeholder:font-semibold placeholder:text-lg py-3 pl-4 text-default"
                  placeholder="Nom du niveau"
              />
            </div>
            <InputCross
                v-else
                v-model="currentCursus.levels[index]"
                @delete="deleteLevel(index)"
                @update:modelValue="(value) => updateLevel(index, value)"
            />
          </template>
          <button
              @click="addLevel"
              class="bg-white text-default border-[gray-300] border mx-auto size-12 rounded-full inline-flex items-center justify-center"
          >
            <PlusLight class="size-5"/>
          </button>
        </div>
        <div class="flex flex-col items-start justify-start">
          <div class="flex flex-col items-center gap-y-2">
            <div class="font-nunito font-medium text-sm text-default">Progression</div>
            <ToogleCursus v-model="currentCursus.progression"/>
          </div>
        </div>
      </div>
      <div class="flex items-center justify-center mt-auto">
        <button @click="handleSave"
                class="bg-default py-2 px-4 font-nunito font-semibold text-white hover:opacity-80 rounded-lg mr-8 transition-colors active:bg-default/80">
          Enregistrer
        </button>
        <button @click="resetForm"
                class=" py-2 px-4  font-nunito font-semibold text-default border hover:opacity-70 bg-white border-placeholder rounded-lg">
          Annuler
        </button>
      </div>
    </div>
  </div>
</template>

