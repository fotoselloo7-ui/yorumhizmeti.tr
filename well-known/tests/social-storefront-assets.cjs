// Format-only export of original generated artwork; no cropping, subject edits or upscaling.
const fs = require('node:fs/promises');
const path = require('node:path');
const sharp = require(process.env.NETVERA_SHARP_PATH || 'sharp');
async function main() {
    const [key, source] = process.argv.slice(2);
    if (!['social-creator', 'video-creators', 'social-community'].includes(key) || !path.isAbsolute(source || '')) throw new Error('Expected asset key and absolute source');
    const dir = path.resolve(__dirname, '../public/assets/img/social-storefront');
    await fs.mkdir(dir, { recursive: true });
    const target = path.join(dir, key + '.webp');
    try { await fs.access(target); throw new Error('Existing asset will not be overwritten'); } catch (error) { if (error.code !== 'ENOENT') throw error; }
    const before = await sharp(source).metadata();
    if (before.width < 1200 || before.height < 800) throw new Error('High resolution original required');
    await sharp(source).webp({ quality: 94, effort: 6 }).toFile(target);
    const after = await sharp(target).metadata();
    if (before.width !== after.width || before.height !== after.height) throw new Error('Native dimensions changed');
    console.log(JSON.stringify({ key, width: after.width, height: after.height, bytes: (await fs.stat(target)).size }));
}
main().catch(error => { console.error(error.message); process.exitCode = 1; });
