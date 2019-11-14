window.hypothesisConfig = function() {
  return {
    "onLayoutChange": function(obj) {
      console.log(`onLayoutChange ${JSON.stringify(obj)}`)
      localStorage.setItem('hypothesisLayout', JSON.stringify(obj))
      hypothesisAdjust()
      },
    "openSidebar": false
  }
}

function delay(seconds) {
  return new Promise(resolve => setTimeout(resolve, seconds * 1000))
 }

async function loadScript() {
  embedScript = document.createElement('script')
  embedScript.src = 'https://hypothes.is/embed.js'
  document.head.appendChild(embedScript)
  await delay(1) 
}

hypothesisWrapper = document.createElement('div')
hypothesisWrapper.id = 'hypothesisWrapper'
hypothesisWrapper.innerHTML = document.body.innerHTML
document.body.innerHTML = hypothesisWrapper.outerHTML

function hypothesisComputeWrapperWidth() {
  const wrapper = document.getElementById('hypothesisWrapper')
  const layout = JSON.parse(localStorage.getItem('hypothesisLayout'))
  const wrapperWidth = wrapper.clientWidth
  const sidebarWidth = layout ? layout.width : 485
  return (wrapperWidth - sidebarWidth )
}

async function hypothesisAdjust() {
  await delay(.1) 
  const wrapper = document.getElementById('hypothesisWrapper')
  wrapper.style.width = '100%'
  wrapper.style.height = '100%'
  wrapper.style.width = `${hypothesisComputeWrapperWidth()}px`
}

window.onresize = hypothesisAdjust
window.onload = hypothesisAdjust

loadScript()


