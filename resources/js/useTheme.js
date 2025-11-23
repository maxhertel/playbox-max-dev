// resources/js/Composables/useTheme.js
import { ref, onMounted, watch } from 'vue';

const theme = ref('light'); // Estado reativo para o tema

export function useTheme() {
    // Função para alternar o tema
    const toggleTheme = () => {
        theme.value = theme.value === 'light' ? 'dark' : 'light';
        localStorage.setItem('user-theme', theme.value); // Salva a preferência no armazenamento local
    };

    // Função para aplicar a classe 'dark' ao elemento raiz (<html>)
    const applyTheme = (currentTheme) => {
        if (currentTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    // Quando o componente é montado, verifica a preferência salva ou do sistema
    onMounted(() => {
        const savedTheme = localStorage.getItem('user-theme');
        if (savedTheme) {
            theme.value = savedTheme;
        } else {
            // Verifica a preferência do sistema operacional como fallback
            theme.value = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        applyTheme(theme.value);
    });

    // Observa mudanças no estado do tema e aplica a classe
    watch(theme, (newTheme) => {
        applyTheme(newTheme);
    });

    return {
        theme,
        toggleTheme,
    };
}
