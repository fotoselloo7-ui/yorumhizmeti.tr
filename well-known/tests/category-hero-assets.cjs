// Format-only export: native resolution and alpha retained; no crop or subject edits.
const fs = require('node:fs/promises');
const path = require('node:path');
const sharp = require(process.env.NETVERA_SHARP_PATH || 'sharp');
const keys = ['audience','engagement','video','audio','community','web','ecommerce','mobile','design','content','search','ads','reputation','local'];
async function main() {
  const [key, source] = process.argv.slice(2);
  if (!keys.includes(key) || !source || !path.isAbsolute(source)) throw new Error('Expected registered family and absolute generated PNG source');
  const root = path.resolve(__dirname, '../public/assets/img/category-heroes');
  await fs.mkdir(root, {recursive:true});
  const target = path.join(root,key+'.webp');
  try { await fs.access(target); throw new Error('Refusing to replace existing final asset: '+key); } catch(e) { if(e.code!=='ENOENT') throw e; }
  const input = await sharp(source).metadata();
  if (!input.hasAlpha || input.width < 1200 || input.height < 800) throw new Error('Generated asset must be high-resolution RGBA');
  await sharp(source).webp({quality:95,alphaQuality:100,effort:6}).toFile(target);
  const output = await sharp(target).metadata();
  if (input.width!==output.width || input.height!==output.height || !output.hasAlpha) throw new Error('Resolution / alpha changed');
  console.log(JSON.stringify({key,width:output.width,height:output.height,bytes:(await fs.stat(target)).size,alpha:output.hasAlpha}));
}
main().catch(e=>{console.error(e.message);process.exitCode=1;});
