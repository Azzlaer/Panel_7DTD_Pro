# PRESENTACION 

![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/01.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/02.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/03.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/04.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/05.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/06.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/07.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/08.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/09.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/010.png)
![Preview](https://github.com/Azzlaer/Panel_7DTD_Pro/blob/main/img/011.png)

# Panel_7DTD_Pro  
Un panel más personalizado para tu servidor de *7 Days to Die* usando Windows

## ✅ ¿Qué es?  
Panel_7DTD_Pro es una interfaz ligera en PHP/Hack diseñada para facilitar la administración de servidores de 7 Days to Die en plataformas Windows. Permite gestionar servidores, guardar jugadores, ver estadísticas, descargar información y mucho más de un modo sencillo.

## 🛠 Características  
- Gestión de múltiples servidores desde un solo panel  
- Guardado automático de información de jugadores (`players_save.php`)  
- Guardado automático de datos de los servidores (`save_servers.php`)  
- Descarga de datos y logs (`download.php`)  
- Autenticación/logout de usuario (`logout.php`)  
- Interfaz web centralizada (`dashboard.php`)  
- Configuración fácil mediante archivo `config.php`  
- Archivo JSON de configuración de servidores (`servers.json`)  
- Front‑end basado en PHP + Hack  
- Compatible exclusivamente con Windows (según diseño original)

## 📂 Estructura del repositorio  
Aquí una vista rápida de los principales ficheros y carpetas:

```
/img/                → Imágenes, logos u otros recursos visuales  
/pages/              → Páginas adicionales del panel (si aplica)  
api.php              → API interna para peticiones AJAX u otras interacciones  
config.php           → Archivo principal de configuración (ruta de servidores, credenciales, etc)  
dashboard.php        → Página principal del panel  
download.php         → Funcionalidad de descarga de datos  
footer.php           → Pie de página común  
header.php           → Cabecera del panel  
index.php            → Página de inicio / login  
logout.php           → Cerrar sesión de usuario  
players_save.php     → Script para guardar datos de los jugadores  
save_servers.php     → Script para guardar datos de servidores  
servers.json         → Lista de servidores configurados en formato JSON  
```

## 🎯 Requisitos  
- Windows (servidor o PC) con entorno web (por ejemplo Apache, IIS o similar)  
- PHP (versión recomendada según tus pruebas)  
- Extensión/hack si usas Hack (aunque la mayoría funciona en PHP puro)  
- Acceso de escritura al directorio donde se guardarán los datos (jugadores, servidores)  
- Permisos adecuados para que el panel acceda/modifique los ficheros de servidor 7 DTD cuando sea necesario

## 🧭 Instalación y configuración rápida  
1. Clona este repositorio o descarga los archivos:  
   ```bash
   git clone https://github.com/Azzlaer/Panel_7DTD_Pro.git
   ```  
2. Copia los archivos al directorio web de tu servidor Windows (por ejemplo `C:\inetpub\wwwroot\panel7dtd\`).  
3. Edita el archivo `config.php` para definir los parámetros de tu entorno: rutas, credenciales, servidores.  
4. Asegúrate de que `servers.json` contiene los servidores que vas a administrar, con el formato correcto.  
5. Ajusta los permisos de los archivos y carpetas para que el panel pueda escribir datos (jugadores, servidores, logs).  
6. Abre tu navegador y navega a `http://<tu_servidor>/panel7dtd/index.php`, inicia sesión y comienza a usar el panel.

## 📝 Uso  
- Ve a “Dashboard” para ver una visión general de tus servidores.  
- Usa “Guardar Servidores” para actualizar la lista de servidores o estados.  
- Usa “Guardar Jugadores” para obtener estadísticas o datos de los jugadores activos.  
- Descarga logs/datos mediante la opción “Download”.  
- Cierra sesión con “Logout”.

## 🎨 Personalización  
El panel viene con un layout básico. Puedes cambiar estilos, logos o cabecera (`header.php`) o personalizar los colores mediante CSS adicional. También puedes ampliar la funcionalidad añadiendo nuevas páginas dentro de `/pages/`.

## 🤝 Contribuciones  
Las contribuciones son bienvenidas. Si quieres sugerir una mejora, informar de un bug o enviar un pull request:

1. Crea un _fork_ del repositorio  
2. Crea una rama de trabajo (`feature/nueva‑funcionalidad`)  
3. Realiza tus cambios y haz un commit con mensaje claro  
4. Envía un _pull request_ para revisión  

## 📄 Licencia  
Este proyecto se publica bajo la licencia **MIT** (o la que decidas). Consulta el archivo `LICENSE` para más detalles.

## 📬 Contacto  
Para dudas, sugerencias o soporte puedes contactar al autor: **Azzlaer** (ver perfil en GitHub)  
¡Gracias por usar Panel_7DTD_Pro y feliz gestión de servidores!
