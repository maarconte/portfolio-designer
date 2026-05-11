#!/bin/bash

# Configuration
THEME_NAME="jeanne"
OUTPUT_FILE="${THEME_NAME}.zip"
TEMP_DIR="temp_export"

echo "📦 Préparation de l'exportation du thème ${THEME_NAME}..."

# Nettoyage des anciens fichiers
rm -f "$OUTPUT_FILE"
rm -rf "$TEMP_DIR"

# Création de la structure temporaire
mkdir -p "$TEMP_DIR/$THEME_NAME"

# Copie des fichiers en excluant les éléments inutiles
# On utilise rsync pour sa gestion simple des exclusions
if command -v rsync >/dev/null 2>&1; then
    rsync -a \
        --exclude='.git/' \
        --exclude='.gitignore' \
        --exclude='.agents/' \
        --exclude='.DS_Store' \
        --exclude='firebase-debug.log' \
        --exclude='skills-lock.json' \
        --exclude='phpunit.xml.dist' \
        --exclude="$TEMP_DIR/" \
        --exclude="*.zip" \
        --exclude="export.sh" \
        ./ "$TEMP_DIR/$THEME_NAME/"
else
    # Fallback si rsync n'est pas dispo (moins précis mais fonctionnel)
    cp -R . "$TEMP_DIR/$THEME_NAME/"
    rm -rf "$TEMP_DIR/$THEME_NAME/.git"
    rm -rf "$TEMP_DIR/$THEME_NAME/.agents"
    rm -f "$TEMP_DIR/$THEME_NAME/.DS_Store"
fi

# Création de l'archive ZIP
if command -v zip >/dev/null 2>&1; then
    echo "🤐 Création de l'archive ZIP..."
    (cd "$TEMP_DIR" && zip -r "../$OUTPUT_FILE" "$THEME_NAME" > /dev/null)
    echo "✅ Exportation terminée : $OUTPUT_FILE"
else
    echo "❌ Erreur : La commande 'zip' est introuvable."
    exit 1
fi

# Nettoyage
rm -rf "$TEMP_DIR"
