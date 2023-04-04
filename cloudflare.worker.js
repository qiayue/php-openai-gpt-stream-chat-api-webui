/*
感谢@赖嘉伟Gary https://github.com/paicha 提供 Worker 代码 

用途：
  使用此方式，不需要购买海外服务器，替换 /class/Class.ChatGPT.php 中的 api.openai.com 域名后，直接将本项目开源代码部署到国内服务器即可使用。

使用方法：
  1、在 CloudFlare 添加一个域名，注意只能填一级域名，不能填子域名，所以可以新注册一个便宜域名专门做这个事情；
  2、添加域名后，在域名注册商处把域名的 dns 设为 CloudFlare 的 dns 地址；
  3、等待域名生效，大概需要十几分钟到一个小时不等；
  4、添加一个 Worker ，把以下代码复制到进去；
  5、点击 Worker 的 Trigger 界面，设置自定义域名，选择第一步添加的域名；
  6、等全部生效后，就可以用你的自定义域名替换 /class/Class.ChatGPT.php 中的 api.openai.com 域名。

*/
async function handleRequest(request) {
  const url = new URL(request.url)
  const fetchAPI = request.url.replace(url.host, 'api.openai.com')

  // 添加跨域处理
  const corsHeaders = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'OPTIONS',
    'Access-Control-Allow-Headers': '*',
  };
  if (request.method === 'OPTIONS') return new Response(null, { headers: corsHeaders });

  return fetch(fetchAPI, { headers: request.headers, method: request.method, body: request.body })
}

addEventListener("fetch", (event) => {
  event.respondWith(handleRequest(event.request))
})