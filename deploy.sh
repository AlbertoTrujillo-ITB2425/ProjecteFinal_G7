#!/bin/bash

# --- COLORES ---
B='\033[0;34m'
G='\033[0;32m'
Y='\033[1;33m'
R='\033[0;31m'
NC='\033[0m'

# Asegurar permisos de ejecución al iniciar
chmod +x scripts/*.sh 2>/dev/null

show_menu() {
    clear
    echo -e "${B}======================================================${NC}"
    echo -e "${B}         SOC CYBERPYME - PANEL DE CONTROL           ${NC}"
    echo -e "${B}======================================================${NC}"
    echo -e "${G}1)${NC} Configurar Infraestructura (Carpetas, SQL, LDAP)"
    echo -e "${G}2)${NC} Generar Archivo de Credenciales (.env)"
    echo -e "${G}3)${NC} Instalar Inteligencia Artificial (Ollama/Modelos)"
    echo -e "${G}4)${NC} Desplegar Contenedores (Docker Compose Up)"
    echo -e "${G}5)${NC} Configurar Seguridad HTTPS (Certbot)"
    echo -e "${G}6)${NC} Ejecutar Respaldo Automático (Git Commit/Push)"
    echo -e "${G}7)${NC} Script de Configuración General (Legacy)"
    echo -e "${R}8) Salir${NC}"
    echo -e "${B}======================================================${NC}"
    echo -n "Seleccione una opción [1-8]: "
}

while true; do
    show_menu
    read -r opt
    case $opt in
        1)
            echo -e "\n${B}[EJECUTANDO]${NC} Setup Infrastructure..."
            ./scripts/setup_infrastructure.sh
            read -p "Presione Enter para volver..."
            ;;
        2)
            echo -e "\n${B}[EJECUTANDO]${NC} Generate ENV..."
            ./scripts/generate_env.sh
            read -p "Presione Enter para volver..."
            ;;
        3)
            echo -e "\n${B}[EJECUTANDO]${NC} Install AI Modules..."
            ./scripts/install_ai.sh
            read -p "Presione Enter para volver..."
            ;;
        4)
            echo -e "\n${B}[EJECUTANDO]${NC} Docker Deployment..."
            docker compose up -d --build
            echo -e "${G}Servicios levantados.${NC}"
            read -p "Presione Enter para volver..."
            ;;
        5)
            echo -e "\n${B}[EJECUTANDO]${NC} Enable HTTPS..."
            ./scripts/enable_https.sh
            read -p "Presione Enter para volver..."
            ;;
        6)
            echo -e "\n${B}[EJECUTANDO]${NC} Auto-Commit Backup..."
            ./scripts/auto_commit.sh
            read -p "Presione Enter para volver..."
            ;;
        7)
            echo -e "\n${B}[EJECUTANDO]${NC} Project Setup..."
            ./scripts/project_setup.sh
            read -p "Presione Enter para volver..."
            ;;
        8)
            echo -e "${G}Saliendo del sistema...${NC}"
            exit 0
            ;;
        *)
            echo -e "${R}Opción no válida.${NC}"
            sleep 1
            ;;
    esac
done
