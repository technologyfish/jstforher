/**
 * 将 pxtovw(数字) 转换为直接的 vw 值
 * 基于 750px 设计稿
 */

const fs = require('fs');
const path = require('path');

// 转换函数
function pxtovw(px) {
  return ((px / 750) * 100).toFixed(2) + 'vw';
}

// 处理文件内容
function convertFile(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');
  let modified = false;

  // 匹配 pxtovw(数字) 的正则
  const regex = /pxtovw\((\d+(?:\.\d+)?)\)/g;

  content = content.replace(regex, (match, px) => {
    modified = true;
    const vw = pxtovw(parseFloat(px));
    console.log(`  ${match} -> ${vw}`);
    return vw;
  });

  if (modified) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`✅ 已更新: ${filePath}`);
    return true;
  }

  return false;
}

// 递归遍历目录
function processDirectory(dir) {
  const files = fs.readdirSync(dir);

  files.forEach(file => {
    const filePath = path.join(dir, file);
    const stat = fs.statSync(filePath);

    if (stat.isDirectory()) {
      // 跳过 node_modules
      if (file !== 'node_modules') {
        processDirectory(filePath);
      }
    } else if (file.endsWith('.vue') || file.endsWith('.scss')) {
      console.log(`\n检查文件: ${filePath}`);
      convertFile(filePath);
    }
  });
}

// 开始处理
console.log('开始转换 pxtovw() 为 vw 值...\n');
processDirectory('./src');
console.log('\n转换完成！');

