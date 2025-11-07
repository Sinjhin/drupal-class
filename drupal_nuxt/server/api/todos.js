export default defineEventHandler(async (event) => {
  const config = useRuntimeConfig()
  const method = event.method
  const query = getQuery(event)

  // Build the Drupal API URL - use HTTP internally since it's server-to-server
  const drupalUrl = 'http://web.4rd.ai:3000/jsonapi/node/todo'

  try {
    if (method === 'GET') {
      // Fetch TODOs
      const response = await fetch(drupalUrl)
      const data = await response.json()
      return data
    } else if (method === 'POST') {
      // Create TODO
      const body = await readBody(event)
      const response = await fetch(drupalUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/vnd.api+json',
          'Accept': 'application/vnd.api+json'
        },
        body: JSON.stringify(body)
      })
      const data = await response.json()
      return data
    } else if (method === 'PATCH') {
      // Update TODO
      const body = await readBody(event)
      const todoId = query.id
      const response = await fetch(`${drupalUrl}/${todoId}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/vnd.api+json',
          'Accept': 'application/vnd.api+json'
        },
        body: JSON.stringify(body)
      })
      const data = await response.json()
      return data
    } else if (method === 'DELETE') {
      // Delete TODO
      const todoId = query.id
      const response = await fetch(`${drupalUrl}/${todoId}`, {
        method: 'DELETE'
      })
      return { success: true }
    }
  } catch (error) {
    console.error('API Error:', error)
    throw createError({
      statusCode: 500,
      message: 'Failed to communicate with Drupal API'
    })
  }
})
