<template>
    <div class="border h-full flex justify-center items-center border-dashed border-gray-900/25 p-6 bg-gray-300 relative"
        @dragover.prevent @drop.prevent="onDrop" :style="backgroundStyle" :class="{ 'animate-scan': isScanning }">
        <div class="col-span-full">
            <div class="mt-2 flex justify-center items-center rounded-lg px-6 py-10">
                <div class="text-center">
                    <svg v-if="!backgroundImage" class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <form>
                        <div v-if="!backgroundImage" class="mt-4 flex text-sm leading-6 text-gray-600">
                            <label for="file-upload"
                                class="relative cursor-pointer rounded-md  font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                <span>Upload a file</span>
                                <input id="file-upload" name="customImage" type="file" class="sr-only"
                                    @change="onFileChange" />
                            </label>
                            <p class="pl-1 ml-1">or drag and drop</p>
                        </div>
                    </form>
                    <p v-if="!backgroundImage" class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 10MB</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import EventBus from '../../event-bus';
import { useToast } from 'vue-toastification';
const customImage = ref(null);
const backgroundImage = ref(null);
const toast = useToast();
let selectedFile = null;
const isScanning = ref(false);
const backgroundStyle = computed(() => {
    return backgroundImage.value ?
        {
            backgroundImage: `url(${backgroundImage.value})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            backgroundRepeat: 'no-repeat',
        } :
        {};
});

const onFileChange = (event) => {
    selectedFile = event.target.files[0];
    if (selectedFile && (selectedFile.size <= 3024 * 4032 * 3)) {
        const reader = new FileReader();
        reader.onload = (e) => {
            backgroundImage.value = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
    } else {
        toast.error('File is too large or not an image.');
    }
};

const onDrop = (event) => {
    selectedFile = event.dataTransfer.files[0];
    if (selectedFile && (selectedFile.size <= 3024 * 4032 * 3)) {
        const reader = new FileReader();
        reader.onload = (e) => {
            backgroundImage.value = e.target.result;
        };
        reader.readAsDataURL(selectedFile);
    } else {
        toast.error('File is too large or not an image.');
    }
};

const submitForm = async () => {
    if (!selectedFile) {
        toast.error('Please select a file before submitting.');
        EventBus.emit('activateScan');
        return;
    }
    const formData = new FormData();
    formData.append('customImage', selectedFile);
    try {
        isScanning.value = true;
        const response = await axios.post('/image-process', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        EventBus.emit('dataReceived', response.data);
    } catch (error) {
        console.error('Error:', error);
    } finally {
        isScanning.value = false;
    }
};



EventBus.on("scan", () => {
    submitForm();
});
</script>

<style scoped>
.animate-scan::before {
    @apply w-full h-2 absolute top-0 left-0;
    @apply bg-gradient-to-b from-transparent via-green-500/75 to-transparent;
    animation: scan 2s ease-in-out infinite alternate;
    content: '';
}

@keyframes scan {
    100% {
        @apply top-full -translate-y-full;
    }
}
</style>