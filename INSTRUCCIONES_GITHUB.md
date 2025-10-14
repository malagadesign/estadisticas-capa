# 📦 Instrucciones para subir el proyecto a GitHub

## ✅ Estado actual

- ✅ Repositorio Git inicializado
- ✅ Commit inicial realizado (364 archivos)
- ✅ Rama `main` configurada
- ✅ Archivos sensibles protegidos en `.gitignore`

## 🔐 Verificación de seguridad

Archivos que **NO SE SUBIERON** (protegidos por .gitignore):
- ❌ `.env` - Variables de entorno con credenciales
- ❌ `mlgcapa_enc.sql` - Base de datos
- ❌ `*.log` - Archivos de logs
- ❌ Archivos de backup (`z$*.php`)

## 📋 Pasos para subir a GitHub

### 1. Crear repositorio en GitHub

1. Ve a [github.com](https://github.com)
2. Haz clic en el botón **"+"** (arriba a la derecha) → **"New repository"**
3. Completa los datos:
   - **Repository name:** `encuestas-capa` (o el nombre que prefieras)
   - **Description:** "Sistema de Encuestas CAPA - Estadísticas"
   - **Visibility:** 
     - ✅ **Private** (recomendado para código privado)
     - ⚠️ **Public** (solo si querés que sea público)
   - **NO** marcar "Initialize with README" (ya tenemos uno)
   - **NO** agregar .gitignore (ya tenemos uno)
   - **NO** agregar licencia
4. Haz clic en **"Create repository"**

### 2. Conectar el repositorio local con GitHub

GitHub te mostrará instrucciones. Usá estos comandos en tu terminal:

```bash
cd /Users/mica/htdocs/capa/encuestas

# Agregar el remoto (reemplazá TU_USUARIO con tu usuario de GitHub)
git remote add origin https://github.com/TU_USUARIO/encuestas-capa.git

# O si usas SSH:
# git remote add origin git@github.com:TU_USUARIO/encuestas-capa.git

# Subir el código
git push -u origin main
```

### 3. Autenticación

GitHub te pedirá autenticación. Opciones:

#### Opción A: Personal Access Token (recomendado)
1. Ve a GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Genera un nuevo token con permisos de `repo`
3. Copia el token
4. Usalo como contraseña cuando te lo pida git

#### Opción B: SSH (alternativa)
```bash
# Generar clave SSH
ssh-keygen -t ed25519 -C "tu-email@ejemplo.com"

# Agregar a GitHub
# Copiar el contenido de ~/.ssh/id_ed25519.pub
cat ~/.ssh/id_ed25519.pub

# Pegarlo en GitHub → Settings → SSH and GPG keys → New SSH key
```

### 4. Verificar que se subió correctamente

```bash
# Ver el estado
git remote -v

# Verificar en GitHub
# Ve a: https://github.com/TU_USUARIO/encuestas-capa
```

## 🔄 Comandos útiles para el futuro

### Hacer cambios y subirlos
```bash
# Ver cambios
git status

# Agregar archivos modificados
git add .

# O agregar archivos específicos
git add config.php usuarios/ADM.php

# Hacer commit
git commit -m "Descripción de los cambios"

# Subir a GitHub
git push
```

### Descargar cambios
```bash
# Si trabajás desde otro lugar
git pull
```

### Ver historial
```bash
git log --oneline
```

### Crear una rama para trabajar
```bash
# Crear y cambiar a nueva rama
git checkout -b feature/nueva-funcionalidad

# Hacer cambios, commit, etc.

# Volver a main
git checkout main

# Fusionar los cambios
git merge feature/nueva-funcionalidad
```

## 📝 Buenas prácticas

### Mensajes de commit
```bash
# Buenos ejemplos:
git commit -m "Agregado sistema de exportación a Excel"
git commit -m "Corregido bug en validación de formularios"
git commit -m "Actualizado dominio a estadistica-capa.org.ar"

# Malos ejemplos:
git commit -m "fix"
git commit -m "cambios"
git commit -m "asdf"
```

### Antes de cada commit
```bash
# 1. Verificar qué archivos cambiaron
git status

# 2. Ver los cambios específicos
git diff

# 3. Asegurarse de NO incluir archivos sensibles
git diff --cached
```

## ⚠️ Importante - Seguridad

### NUNCA subir:
- ❌ Archivo `.env` con credenciales reales
- ❌ Dumps de base de datos con datos reales
- ❌ Logs del sistema
- ❌ Archivos de backup con datos sensibles

### Si accidentalmente subiste algo sensible:

```bash
# Opción 1: Eliminar del último commit (si no hiciste push)
git reset HEAD~1

# Opción 2: Si ya hiciste push, eliminar del historial
# IMPORTANTE: Esto reescribe el historial
git filter-branch --index-filter 'git rm --cached --ignore-unmatch ruta/archivo.env' HEAD

# Opción 3: Mejor práctica - rotar las credenciales
# 1. Cambiar todas las contraseñas expuestas
# 2. Eliminar el archivo del repo
# 3. Agregarlo a .gitignore
```

## 🔒 Configuración de repositorio privado

Si el repositorio es privado, asegúrate de:
1. Solo dar acceso a personas de confianza
2. Usar autenticación de dos factores en GitHub
3. Revisar periódicamente quién tiene acceso

## 📧 Configurar GitHub para el equipo

Si van a trabajar varias personas:

1. **Settings** → **Manage access** → **Invite a collaborator**
2. Configurar protección de ramas:
   - **Settings** → **Branches** → **Add rule**
   - Proteger `main` de pushes directos
   - Requerir Pull Requests para cambios

## 🆘 Problemas comunes

### Error: "remote origin already exists"
```bash
git remote remove origin
git remote add origin https://github.com/TU_USUARIO/encuestas-capa.git
```

### Error: "failed to push some refs"
```bash
# Primero traer los cambios
git pull origin main --allow-unrelated-histories

# Luego subir
git push origin main
```

### Olvidé agregar algo al .gitignore
```bash
# Agregar al .gitignore
echo ".env" >> .gitignore
echo "config.local.php" >> .gitignore

# Eliminar del índice pero mantener en disco
git rm --cached .env
git rm --cached config.local.php

# Commit
git commit -m "Actualizado .gitignore"
```

## 📚 Recursos

- [GitHub Docs](https://docs.github.com)
- [Git Cheat Sheet](https://education.github.com/git-cheat-sheet-education.pdf)
- [Aprende Git](https://learngitbranching.js.org/)

---

**Última actualización:** Octubre 2025

